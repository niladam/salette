<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Senders;

use Salette\Http\Response;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Salette\Contracts\Sender;
use Salette\Data\FactoryCollection;
use Salette\Requests\PendingRequest;
use Salette\Senders\GuzzleMultipartBodyFactory;

class ArraySender implements Sender
{
    /**
     * Get the factory collection
     */
    public function getFactoryCollection(): FactoryCollection
    {
        $factory = new HttpFactory();

        return new FactoryCollection(
            $factory,
            $factory,
            $factory,
            $factory,
            new GuzzleMultipartBodyFactory(),
        );
    }

    /**
     * Send the request synchronously
     */
    public function send(PendingRequest $pendingRequest): Response
    {
        /** @var class-string<Response> $responseClass */
        $responseClass = $pendingRequest->getResponseClass();

        return $responseClass::fromPsrResponse(
            new GuzzleResponse(
                200,
                ['X-Fake' => true],
                'Default'
            ),
            $pendingRequest,
            $pendingRequest->createPsrRequest()
        );
    }

    /**
     * Send the request asynchronously
     */
    public function sendAsync(PendingRequest $pendingRequest): PromiseInterface
    {
        //
    }
}
