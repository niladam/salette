<?php

declare(strict_types=1);

use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\UserRequest;

test('you can provide a mock client on a connector and all requests will be mocked', function () {
    $mockClient = new MockClient([
        MockResponse::make(['name' => 'Sam']),
        MockResponse::make(['name' => 'Mantas']),
    ]);

    $connector = new TestConnector();
    $connector->withMockClient($mockClient);

    $responseA = $connector->send(new UserRequest());
    $responseB = $connector->send(new UserRequest());

    expect($responseA->isMocked())
        ->toBeTrue()
        ->and($responseB->isMocked())->toBeTrue();
});

test('you can provide a mock client on a request and all requests will be mocked', function () {
    $mockClient = new MockClient([
        MockResponse::make(['name' => 'Sam']),
    ]);

    $request = new UserRequest();
    $request->withMockClient($mockClient);

    $response = connector()->send($request);

    expect($response->isMocked())->toBeTrue();
});

test('request mock clients are always prioritized', function () {
    $mockClientA = new MockClient([
        MockResponse::make(['name' => 'Sam']),
    ]);

    $mockClientB = new MockClient([
        MockResponse::make(['name' => 'Mantas']),
    ]);

    $connector = new TestConnector();
    $connector->withMockClient($mockClientA);

    $request = new UserRequest();
    $request->withMockClient($mockClientB);

    $response = $connector->send($request);

    expect($response->isMocked())
        ->toBeTrue()
        ->and($response->json())->toEqual(['name' => 'Mantas']);
});
