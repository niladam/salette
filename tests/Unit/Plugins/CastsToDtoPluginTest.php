<?php

declare(strict_types=1);

use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Tests\Fixtures\Connectors\DtoConnector;
use Salette\Tests\Fixtures\Data\ApiResponse;
use Salette\Tests\Fixtures\Data\User;
use Salette\Tests\Fixtures\Requests\DTORequest;
use Salette\Tests\Fixtures\Requests\UserRequest;

test('it can cast to a dto that is defined on the request', function () {
    $mockClient = new MockClient([
        new MockResponse(['name' => 'Sammyjo20', 'actual_name' => 'Sam Carré', 'twitter' => '@carre_sam']),
    ]);

    $response = connector()->send(new DTORequest(), $mockClient);
    $dto = $response->dto();
    $json = $response->json();

    expect($response->isMocked())
        ->toBeTrue()
        ->and($dto)->toBeInstanceOf(User::class)
        ->and($dto)->name
        ->toEqual($json['name'])
        ->and($dto)->actualName
        ->toEqual($json['actual_name'])
        ->and($dto)->twitter->toEqual($json['twitter']);
});

test('it can cast to a dto that is defined on a connector', function () {
    $mockClient = new MockClient([
        new MockResponse(['name' => 'Sammyjo20', 'actual_name' => 'Sam Carré', 'twitter' => '@carre_sam']),
    ]);

    $connector = new DtoConnector();

    $response = $connector->send(new UserRequest(), $mockClient);
    $dto = $response->dto();

    expect($dto)
        ->toBeInstanceOf(ApiResponse::class)
        ->and($dto)->data->toEqual($response->json());
});

test('the request dto will be returned as a higher priority than the connector dto', function () {
    $mockClient = new MockClient([
        new MockResponse(['name' => 'Sammyjo20', 'actual_name' => 'Sam Carré', 'twitter' => '@carre_sam']),
    ]);

    $connector = new DtoConnector();

    $response = $connector->send(new DTORequest(), $mockClient);
    $dto = $response->dto();
    $json = $response->json();

    expect($dto)
        ->toBeInstanceOf(User::class)
        ->and($dto)->name
        ->toEqual($json['name'])
        ->and($dto)->actualName
        ->toEqual($json['actual_name'])
        ->and($dto)->twitter->toEqual($json['twitter']);
});

test('you can use the dtoOrFail method to throw an exception if the response has failed', function () {
    $mockClient = new MockClient([
        new MockResponse(['message' => 'Server Error'], 500),
    ]);

    $response = connector()->send(new DTORequest(), $mockClient);

    $this->expectException(LogicException::class);
    $this->expectExceptionMessage('Unable to create data transfer object as the response has failed.');

    $response->dtoOrFail();
});
