<?php

declare(strict_types=1);

namespace Salette\Traits\Connector;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\RejectedPromise;
use Salette\Exceptions\PendingRequestException;
use Salette\Http\Faking\FakeResponse;
use Salette\Http\Faking\MockResponse;
use Salette\Http\Response;
use Salette\Requests\PendingRequest;
use Throwable;

trait ManagesFakeResponses
{
    /**
     * Create the fake response
     *
     * @throws PendingRequestException
     * @throws \Throwable
     */
    protected function createFakeResponse(PendingRequest $pendingRequest)
    {
        $fakeResponse = $pendingRequest->getFakeResponse();

        if (! $fakeResponse instanceof FakeResponse) {
            throw new PendingRequestException('Unable to create fake response because there is no fake response data.');
        }

        $isAsynchronous = $pendingRequest->isAsynchronous();

        // Check if the FakeResponse throws an exception. If the request is
        // asynchronous, then we should allow the promise handler to deal with the exception.

        $exception = $fakeResponse->getException($pendingRequest);

        if ($exception instanceof Throwable && $isAsynchronous === false) {
            throw $exception;
        }

        // Let's create our response!

        $factories = $pendingRequest->getFactoryCollection();

        $response = $fakeResponse->createPsrResponse(
            $factories->responseFactory,
            $factories->streamFactory,
        );

        /**
         * @var class-string<Response> $responseClass
         */
        $responseClass = $pendingRequest->getResponseClass();

        $response = $responseClass::fromPsrResponse(
            $response,
            $pendingRequest,
            $pendingRequest->createPsrRequest(),
            $exception,
        );

        $response->setFakeResponse($fakeResponse);

        if ($fakeResponse instanceof MockResponse) {
            $mockClient = $pendingRequest->getMockClient();

            if ($mockClient !== null) {
                $mockClient->recordResponse($response);
            }

            $response->setMocked(true);
        }

        // When the request isn't async we'll just return the response

        if ($isAsynchronous === false) {
            return $response;
        }

        // When mocking asynchronous requests we need to wrap the response
        // in FulfilledPromise or RejectedPromise depending on if the
        // response has an exception.

        $exception ??= $response->toException();

        return is_null($exception) ? new FulfilledPromise($response) : new RejectedPromise($exception);
    }
}
