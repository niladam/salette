<?php

declare(strict_types=1);

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\RequestInterface;
use Salette\Http\Faking\MockResponse;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\UserRequest;

test('default guzzle config options are sent', function () {
    $connector = new TestConnector();

    $connector->sender()->addMiddleware(function (callable $handler) {
        return function (RequestInterface $guzzleRequest, array $options) {
            expect($options)
                ->toHaveKey('http_errors', true)
                ->and($options)->toHaveKey('connect_timeout', 10)
                ->and($options)->toHaveKey('timeout', 30);

            $factory = new HttpFactory();

            return new FulfilledPromise(MockResponse::make()->createPsrResponse($factory, $factory));
        };
    });

    $connector->send(new UserRequest());
});

test('you can pass additional guzzle config options and they are merged from the connector and request', function () {
    $connector = new TestConnector();

    $connector->config()->add('debug', true);

    $connector->sender()->addMiddleware(function (callable $handler) {
        return function (RequestInterface $guzzleRequest, array $options) {
            expect($options)
                ->toHaveKey('http_errors', true)
                ->and($options)->toHaveKey('connect_timeout', 10)
                ->and($options)->toHaveKey('timeout', 30)
                ->and($options)->toHaveKey('debug', true)
                ->and($options)->toHaveKey('verify', false);

            $factory = new HttpFactory();

            return new FulfilledPromise(MockResponse::make()->createPsrResponse($factory, $factory));
        };
    });

    $request = new UserRequest();

    $request->config()->add('verify', false);

    $connector->send($request);
});
