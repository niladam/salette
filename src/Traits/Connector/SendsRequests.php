<?php

declare(strict_types=1);

namespace Salette\Traits\Connector;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use LogicException;
use Salette\Exceptions\FatalRequestException;
use Salette\Exceptions\RequestException;
use Salette\Http\Response;
use Salette\Requests\PendingRequest;
use Salette\Requests\Request;
use Throwable;

trait SendsRequests
{
    use HasSender;

    public function send(Request $request, ?callable $handleRetry = null): Response
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
                $pendingRequest = $this->createPendingRequest($request);

                $response = $this->sender()->send($pendingRequest);

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
    public function sendAsync(Request $request): PromiseInterface
    {
        $sender = $this->sender();

        return Utils::task(function () use ($request, $sender) {
            $pendingRequest = $this->createPendingRequest($request)->setAsynchronous(true);
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
        bool $useExponentialBackoff = false
    ): Response {
        $request->tries = $tries;
        $request->retryInterval = $interval;
        $request->throwOnMaxTries = $throw;
        $request->useExponentialBackoff = $useExponentialBackoff;

        return $this->send($request, $handleRetry);
    }

    /**
     * Create a new PendingRequest
     */
    public function createPendingRequest(Request $request): PendingRequest
    {
        return new PendingRequest($this, $request);
    }
}
