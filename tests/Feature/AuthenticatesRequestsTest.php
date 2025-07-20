<?php

declare(strict_types=1);

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Salette\Http\Faking\MockResponse;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\UserRequest;

test('you can provide digest authentication and guzzle will send it', function () {
    $connector = new TestConnector();
    $request = new UserRequest();

    $request->withDigestAuth('Sammyjo20', 'Cowboy1', 'Howdy');

    $asserted = false;

    $connector->sender()->addMiddleware(function (callable $handler) use (&$asserted) {
        return function (RequestInterface $guzzleRequest, array $options) use (&$asserted) {
            expect($options)->toHaveKey(RequestOptions::AUTH, [
                'Sammyjo20',
                'Cowboy1',
                'Howdy',
            ]);

            $asserted = true;

            $factory = new HttpFactory();

            return new FulfilledPromise(MockResponse::make()->createPsrResponse($factory, $factory));
        };
    });

    $connector->send($request);

    expect($asserted)->toBeTrue();
});
