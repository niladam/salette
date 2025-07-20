<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Senders;

use GuzzleHttp\Psr7\HttpFactory;
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
    public function send(PendingRequest $pendingRequest): \Salette\Http\Response
    {
        /** @var class-string<\Salette\Http\Response> $responseClass */
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
    public function sendAsync(PendingRequest $pendingRequest): \GuzzleHttp\Promise\PromiseInterface
    {
        //
    }
}
