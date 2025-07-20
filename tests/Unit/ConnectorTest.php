<?php

declare(strict_types=1);

use GuzzleHttp\Promise\Promise;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Http\Response;
use Salette\Tests\Fixtures\Connectors\RequestSelectionConnector;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\HasConnectorUserRequest;
use Salette\Tests\Fixtures\Requests\UserRequest;

test('a connector class can be instantiated using the make method', function () {
    $connectorA = TestConnector::make();

    expect($connectorA)->toBeInstanceOf(TestConnector::class);

    $connectorB = RequestSelectionConnector::make('yee-haw-1-2-3');

    expect($connectorB)
        ->toBeInstanceOf(RequestSelectionConnector::class)
        ->and($connectorB)->apiKey->toEqual('yee-haw-1-2-3');
});

test('the same connector instance is kept if you instantiate it on the request with HasConnector', function () {
    $request = new HasConnectorUserRequest();
    $connector = $request->connector();

    expect($connector)->toBe($request->connector());
});

test('you can send a request through the connector', function () {
    $mockClient = new MockClient([
        new MockResponse(['name' => 'Sammyjo20', 'actual_name' => 'Sam Carré', 'twitter' => '@carre_sam']),
    ]);

    $connector = new TestConnector();
    $response = $connector->send(new UserRequest(), $mockClient);

    expect($response)
        ->toBeInstanceOf(Response::class)
        ->and($response->json())->toEqual(
            ['name' => 'Sammyjo20', 'actual_name' => 'Sam Carré', 'twitter' => '@carre_sam']
        );
});

test('you can send an asynchronous request through the connector', function () {
    $mockClient = new MockClient([
        new MockResponse(['name' => 'Sammyjo20', 'actual_name' => 'Sam Carré', 'twitter' => '@carre_sam']),
    ]);

    $connector = new TestConnector();
    $promise = $connector->sendAsync(new UserRequest(), $mockClient);

    expect($promise)->toBeInstanceOf(Promise::class);

    $response = $promise->wait();

    expect($response)
        ->toBeInstanceOf(Response::class)
        ->and($response->json())->toEqual(
            ['name' => 'Sammyjo20', 'actual_name' => 'Sam Carré', 'twitter' => '@carre_sam']
        );
});
