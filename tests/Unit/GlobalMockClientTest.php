<?php

declare(strict_types=1);

use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\UserRequest;

afterEach(function () {
    MockClient::destroyGlobal();
});

test('can create a global mock client', function () {
    $mockClient = MockClient::global([
        MockResponse::make(['name' => 'Sam']),
    ]);

    expect($mockClient)
        ->toBeInstanceOf(MockClient::class)
        ->and(MockClient::getGlobal())->toBe($mockClient);

    $connector = new TestConnector();
    $response = $connector->send(new UserRequest());

    expect($response->isMocked())
        ->toBeTrue()
        ->and($response->json())->toEqual(['name' => 'Sam']);

    $mockClient->assertSent(UserRequest::class);
});

test('the mock client can be destroyed', function () {
    $mockClient = MockClient::global();

    expect(MockClient::getGlobal())->toBe($mockClient);

    MockClient::destroyGlobal();

    expect(MockClient::getGlobal())->toBeNull();
});

test('a local mock client is given priority over the global mock client', function () {
    MockClient::global([
        MockResponse::make(['name' => 'Sam']),
    ]);

    $localMockClient = new MockClient([
        MockResponse::make(['name' => 'Taylor']),
    ]);

    $connector = new TestConnector();
    $connector->withMockClient($localMockClient);

    $response = $connector->send(new UserRequest());

    expect($response->isMocked())
        ->toBeTrue()
        ->and($response->json())->toEqual(['name' => 'Taylor']);

    $localMockClient->assertSentCount(1);
    MockClient::global()->assertNothingSent();
});
