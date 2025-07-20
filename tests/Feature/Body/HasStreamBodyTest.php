<?php

declare(strict_types=1);

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\RequestInterface;
use Salette\Http\Faking\MockResponse;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\HasStreamBodyRequest;

test('the default body is loaded', function () {
    $request = new HasStreamBodyRequest();

    expect($request->body()->all())->toBeResource();
});

test('the guzzle sender properly sends it', function () {
    $connector = new TestConnector();
    $request = new HasStreamBodyRequest();

    $request->headers()->add('Content-Type', 'application/custom');

    $asserted = false;

    $connector->sender()->addMiddleware(function (callable $handler) use (&$asserted) {
        return function (RequestInterface $guzzleRequest, array $options) use (&$asserted) {
            expect($guzzleRequest->getHeader('Content-Type'))
                ->toEqual(['application/custom'])
                ->and((string) $guzzleRequest->getBody())->toEqual('Howdy, Partner');

            $asserted = true;

            $factory = new HttpFactory();

            return new FulfilledPromise(MockResponse::make()->createPsrResponse($factory, $factory));
        };
    });

    $connector->send($request);

    expect($asserted)->toBeTrue();
});
