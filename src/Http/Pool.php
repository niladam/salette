<?php

declare(strict_types=1);

namespace Salette\Http;

use Closure;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Promise\PromiseInterface;
use Salette\Exceptions\InvalidPoolItemException;
use Salette\Requests\Request;
use Traversable;

class Pool
{
    /**
     * Requests inside the pool
     *
     * @var iterable<PromiseInterface|Request>
     */
    protected iterable $requests;

    /**
     * Handle Response Callback
     *
     * @var Closure(Response, array-key, PromiseInterface): (void)|null
     */
    protected ?Closure $responseHandler = null;

    /**
     * Handle Exception Callback
     *
     * @var Closure(mixed, array-key, PromiseInterface): (void)|null
     */
    protected ?Closure $exceptionHandler = null;

    /**
     * Connector
     */
    protected Connector $connector;

    /**
     * Concurrency
     *
     * How many requests will be sent at once.
     *
     * @var int|Closure(int): int
     */
    protected $concurrency;

    public function __construct(
        Connector $connector,
        $requests = [],
        $concurrency = 5,
        $responseHandler = null,
        $exceptionHandler = null
    ) {
        $this->connector = $connector;
        $this->setRequests($requests);
        $this->setConcurrency($concurrency);

        if (! is_null($responseHandler)) {
            $this->withResponseHandler($responseHandler);
        }

        if (! is_null($exceptionHandler)) {
            $this->withExceptionHandler($exceptionHandler);
        }
    }

    /**
     * Specify a callback to happen for each successful request
     */
    public function withResponseHandler(callable $callable): self
    {
        $this->responseHandler = Closure::fromCallable($callable);

        return $this;
    }

    /**
     * Specify a callback to happen for each failed request
     *
     * @param  callable(mixed $reason, array-key $key, PromiseInterface $poolAggregate): (void)  $callable
     */
    public function withExceptionHandler(callable $callable): self
    {
        $this->exceptionHandler = Closure::fromCallable($callable);

        return $this;
    }

    /**
     * Set the amount of concurrent requests that should be sent
     *
     * @param  int|callable(int $pendingRequests): (int)  $concurrency
     */
    public function setConcurrency($concurrency): self
    {
        $this->concurrency = is_callable($concurrency) ? Closure::fromCallable($concurrency) : $concurrency;

        return $this;
    }

    /**
     * Set the requests
     *
     * @param  iterable|callable  $requests
     */
    public function setRequests($requests): self
    {
        if (is_callable($requests)) {
            $requests = $requests($this->connector);
        }

        if (is_array($requests) || $requests instanceof Traversable) {
            $requestsIterable = $requests;

            $requests = function () use ($requestsIterable) {
                foreach ($requestsIterable as $key => $item) {
                    yield $key => $item;
                }
            };
        }

        $this->requests = $requests();

        return $this;
    }

    /**
     * Get the request generator
     *
     * @return iterable<PromiseInterface|Request>
     */
    public function getRequests(): iterable
    {
        return $this->requests;
    }

    /**
     * Send the pool and create a Promise
     *
     * @throws InvalidPoolItemException
     */
    public function send(): PromiseInterface
    {
        // Iterate through the existing generator and "prepare" the requests.
        // If they are SaletteRequests then we should convert them into
        // promises.

        $preparedRequests = function (): \Generator {
            foreach ($this->requests as $key => $request) {
                if ($request instanceof Request) {
                    yield $key => $this->connector->sendAsync($request);
                } elseif ($request instanceof PromiseInterface) {
                    yield $key => $request;
                } else {
                    throw new InvalidPoolItemException();
                }
            }
        };

        // Next we'll use an EachPromise which accepts an iterator of
        // requests and will process them as the concurrency we set.

        $eachPromise = new EachPromise(
            $preparedRequests(), [
                'concurrency' => $this->concurrency,
                'fulfilled' => $this->responseHandler,
                'rejected' => $this->exceptionHandler,
            ]
        );

        return $eachPromise->promise();
    }
}
