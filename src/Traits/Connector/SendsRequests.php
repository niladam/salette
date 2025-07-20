<?php

declare(strict_types=1);

namespace Salette\Traits\Connector;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use LogicException;
use Salette\Exceptions\DuplicatePipeNameException;
use Salette\Exceptions\FatalRequestException;
use Salette\Exceptions\RequestException;
use Salette\Http\Connector;
use Salette\Http\Faking\MockClient;
use Salette\Http\Pool;
use Salette\Http\Response;
use Salette\Requests\PendingRequest;
use Salette\Requests\Request;
use Throwable;

trait SendsRequests
{
    use HasSender;
    use ManagesFakeResponses;

    public function send(Request $request, ?MockClient $mockClient = null, ?callable $handleRetry = null): Response
    {
        if (is_null($handleRetry)) {
            $handleRetry = static fn (): bool => true;
        }

        $attempts = 0;
        $maxTries = $request->tries ?? $this->tries ?? 1;
        $retryInterval = $request->retryInterval ?? $this->retryInterval ?? 0;
        $throwOnMaxTries = $request->throwOnMaxTries ?? $this->throwOnMaxTries ?? true;
        $useExponentialBackoff = $request->useExponentialBackoff ?? $this->useExponentialBackoff ?? false;

        $maxTries = $maxTries > 0 ? $maxTries : 1;
        $retryInterval = $retryInterval > 0 ? $retryInterval : 0;

        while ($attempts < $maxTries) {
            $attempts++;

            if ($attempts > 1) {
                $sleepTime = $useExponentialBackoff
                    ? $retryInterval * (2 ** ($attempts - 2)) * 1000
                    : $retryInterval * 1000;

                usleep($sleepTime);
            }

            try {
                $pendingRequest = $this->createPendingRequest($request, $mockClient);

                // Check if we should use a fake response
                if ($pendingRequest->hasFakeResponse()) {
                    $response = $this->createFakeResponse($pendingRequest);
                } else {
                    $response = $this->sender()->send($pendingRequest);
                }

                $response = $pendingRequest->executeResponsePipeline($response);

                if ($maxTries > 1) {
                    $response->throw();
                }

                return $response;
            } catch (FatalRequestException|RequestException $exception) {
                $exceptionResponse = $exception instanceof RequestException
                    ? $exception->getResponse()
                    : null;

                if ($exception instanceof FatalRequestException) {
                    $exception->getPendingRequest()->executeFatalPipeline($exception);
                }

                if ($attempts === $maxTries) {
                    if (isset($exceptionResponse) && $throwOnMaxTries === false) {
                        return $exceptionResponse;
                    }
                    throw $exception;
                }

                $allowRetry = $handleRetry($exception, $request)
                              && $request->handleRetry($exception, $request)
                              && $this->handleRetry($exception, $request);

                if ($allowRetry === false) {
                    if (isset($exceptionResponse) && $throwOnMaxTries === false) {
                        return $exceptionResponse;
                    }
                    throw $exception;
                }
            }
        }

        throw new LogicException('The request was not sent.');
    }

    /**
     * Send a request asynchronously
     */
    public function sendAsync(Request $request, ?MockClient $mockClient = null): PromiseInterface
    {
        $sender = $this->sender();

        return Utils::task(function () use ($mockClient, $request, $sender) {
            $pendingRequest = $this->createPendingRequest($request, $mockClient)->setAsynchronous(true);
            $requestPromise = $sender->sendAsync($pendingRequest);

            $requestPromise->then(
                fn (Response $response) => $pendingRequest->executeResponsePipeline($response)
            );

            return $requestPromise;
        });
    }

    /**
     * Send a synchronous request and retry if it fails
     *
     * @param  callable(Throwable,Request):bool|null  $handleRetry
     *
     * @deprecated This will be removed in a future version.
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function sendAndRetry(
        Request $request,
        int $tries,
        int $interval = 0,
        ?callable $handleRetry = null,
        bool $throw = true,
        ?MockClient $mockClient = null,
        bool $useExponentialBackoff = false
    ): Response {
        $request->tries = $tries;
        $request->retryInterval = $interval;
        $request->throwOnMaxTries = $throw;
        $request->useExponentialBackoff = $useExponentialBackoff;

        return $this->send($request, $mockClient, $handleRetry);
    }

    /**
     * Create a new PendingRequest
     *
     * @throws DuplicatePipeNameException
     */
    public function createPendingRequest(Request $request, ?MockClient $mockClient = null): PendingRequest
    {
        return new PendingRequest($this, $request, $mockClient);
    }

    /**
     * Create a request pool
     *
     * @param  iterable<PromiseInterface|Request>|callable(Connector): iterable<PromiseInterface|Request>  $requests
     * @param  int|callable(int $pendingRequests): (int)  $concurrency
     * @param  callable(Response, array-key $key, PromiseInterface $poolAggregate): (void)|null  $responseHandler
     * @param  callable(mixed $reason, array-key $key, PromiseInterface $poolAggregate): (void)|null  $exceptionHandler
     */
    public function pool(
        $requests = [],
        $concurrency = 5,
        ?callable $responseHandler = null,
        ?callable $exceptionHandler = null
    ): Pool {
        return new Pool($this, $requests, $concurrency, $responseHandler, $exceptionHandler);
    }
}
