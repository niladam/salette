<?php

declare(strict_types=1);

use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Repositories\StringBodyRepository;
use Salette\Tests\Fixtures\Responses\UserData;
use Salette\Tests\Fixtures\Responses\UserResponse;
use Salette\Tests\Fixtures\Requests\UserRequestWithCustomResponse;

test('pulling a response from the sequence will return the correct response', function () {
    $responseA = MockResponse::make();
    $responseB = MockResponse::make([], 500);
    $responseC = MockResponse::make([], 500);

    $mockClient = new MockClient([$responseA, $responseB, $responseC]);

    expect($mockClient->getNextFromSequence()->status())
        ->toEqual($responseA->status())
        ->and($mockClient->getNextFromSequence()->status())->toEqual($responseB->status())
        ->and($mockClient->getNextFromSequence()->status())->toEqual($responseC->status())
        ->and($mockClient->isEmpty())->toBeTrue();
});

test('a mock response can have raw body data', function () {
    $response = MockResponse::make('xml', 200, ['Content-Type' => 'application/json']);

    expect($response->headers()->all())
        ->toEqual(['Content-Type' => 'application/json'])
        ->and($response->status())->toEqual(200)
        ->and($response->body())->toBeInstanceOf(StringBodyRepository::class)
        ->and($response->body()->all())->toEqual('xml');
});

test('a response can be a custom response class', function () {
    $mockClient = new MockClient([MockResponse::make(['foo' => 'bar'])]);
    $request = new UserRequestWithCustomResponse();

    $response = connector()->send($request, $mockClient);

    expect($response)
        ->toBeInstanceOf(UserResponse::class)
        ->and($response)->customCastMethod()->toBeInstanceOf(UserData::class)
        ->and($response)->foo()->toBe('bar');
});
