<?php

declare(strict_types=1);

use Salette\Http\Faking\FakeResponse;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\UserRequest;

test('if a simulated response payload was provided before mock response it will take priority', function () {
    $mockClient = new MockClient([
        new MockResponse(['name' => 'Sam'], 200, ['X-Greeting' => 'Howdy']),
    ]);

    $fakeResponse = new FakeResponse(['name' => 'Gareth'], 201, ['X-Greeting' => 'Hello']);

    $request = new UserRequest();
    $request->middleware()->onRequest(fn () => $fakeResponse);

    $response = TestConnector::make()->send($request, $mockClient);

    expect($response->json())
        ->toEqual(['name' => 'Gareth'])
        ->and($response->status())->toEqual(201)
        ->and($response->header('X-Greeting'))->toEqual('Hello');
});
