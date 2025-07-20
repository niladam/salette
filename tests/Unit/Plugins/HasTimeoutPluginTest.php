<?php

declare(strict_types=1);

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Salette\Http\Faking\MockResponse;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Connectors\TimeoutConnector;
use Salette\Tests\Fixtures\Requests\TimeoutRequest;
use Salette\Tests\Fixtures\Requests\UserRequest;

test('a request is given a default timeout and connect timeout', function () {
    $connector = new TestConnector();
    $request = UserRequest::make();

    $connector->sender()->addMiddleware(function (callable $handler) {
        return function (RequestInterface $request, array $options) {
            expect($options['connect_timeout'])
                ->toEqual(10)
                ->and($options['timeout'])->toEqual(30);

            $factory = new HttpFactory();

            return new FulfilledPromise(MockResponse::make()->createPsrResponse($factory, $factory));
        };
    }, 'test');

    $connector->send($request);
});

test('a request can set a timeout and connect timeout', function () {
    $request = new TimeoutRequest();
    $pendingRequest = connector()->createPendingRequest($request);

    $config = $pendingRequest->config()->all();

    expect($config)
        ->toHaveKey('connect_timeout', 1)
        ->and($config)->toHaveKey('timeout', 2);
});

test('a connector is given a default timeout and connect timeout', function () {
    $connector = new TimeoutConnector();

    $connector->sender()->addMiddleware(function (callable $handler) {
        return function (RequestInterface $request, array $options) {
            expect($options['connect_timeout'])
                ->toEqual(10.0)
                ->and($options['timeout'])->toEqual(5.0);

            return new FulfilledPromise(new Response());
        };
    });

    $pendingRequest = $connector->createPendingRequest(new UserRequest());

    $config = $pendingRequest->config()->all();

    expect($config)
        ->toHaveKey('connect_timeout', 10)
        ->and($config)->toHaveKey('timeout', 5);

    $connector->send(new UserRequest());
});
