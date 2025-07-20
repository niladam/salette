<?php

declare(strict_types=1);

use GuzzleHttp\Promise\PromiseInterface;
use Salette\Exceptions\RequestException;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Http\Response;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\ErrorRequest;
use Salette\Tests\Fixtures\Requests\UserRequest;
use Salette\Tests\Fixtures\Requests\UserRequestWithCustomResponse;
use Salette\Tests\Fixtures\Responses\UserData;
use Salette\Tests\Fixtures\Responses\UserResponse;

test('an asynchronous request can be made successfully', function () {
    $promise = TestConnector::make()->sendAsync(new UserRequest());

    expect($promise)->toBeInstanceOf(PromiseInterface::class);

    $response = $promise->wait();

    expect($response)->toBeInstanceOf(Response::class);

    $data = $response->json();

    expect($response->getPendingRequest()->isAsynchronous())
        ->toBeTrue()
        ->and($response->isMocked())->toBeFalse()
        ->and($response->status())->toEqual(200)
        ->and($data)->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);
});

test('an asynchronous request can handle an exception properly', function () {
    $promise = TestConnector::make()->sendAsync(new ErrorRequest());

    $this->expectException(RequestException::class);

    $promise->wait();
});

test('an asynchronous response will still be passed through response middleware', function () {
    $mockClient = new MockClient([
        MockResponse::make(['name' => 'Sam']),
    ]);

    $request = new UserRequest();

    $passed = false;

    $request->middleware()->onResponse(function (Response $response) use (&$passed) {
        $passed = true;
    });

    $connector = new TestConnector();

    $promise = $connector->sendAsync($request, $mockClient);
    $response = $promise->wait();

    expect($passed)->toBeTrue();
});

test('an asynchronous request will return a custom response', function () {
    $mockClient = new MockClient([
        MockResponse::make(['foo' => 'bar']),
    ]);

    $connector = new TestConnector();
    $request = new UserRequestWithCustomResponse();

    $promise = $connector->sendAsync($request, $mockClient);

    $response = $promise->wait();

    expect($response)
        ->toBeInstanceOf(UserResponse::class)
        ->and($response)->customCastMethod()->toBeInstanceOf(UserData::class)
        ->and($response)->foo()->toBe('bar');
});

test('middleware is only executed when an asynchronous request is sent', function () {
    $mockClient = new MockClient([
        MockResponse::make(['foo' => 'bar']),
    ]);

    $request = new UserRequest();
    $request->withMockClient($mockClient);
    $sent = false;

    $request->middleware()->onRequest(function () use (&$sent) {
        $sent = true;
    });

    $promise = TestConnector::make()->sendAsync($request);

    expect($sent)->toBeFalse();

    $promise->wait();

    expect($sent)->toBeTrue();
});
