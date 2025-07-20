<?php

declare(strict_types=1);

use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Tests\Fixtures\Data\User;
use Salette\Contracts\DataObjects\WithResponse;
use Salette\Tests\Fixtures\Requests\DTORequest;
use Salette\Tests\Fixtures\Data\UserWithResponse;
use Salette\Tests\Fixtures\Requests\DTOWithResponseRequest;

test('if a dto does not implement the WithResponse interface and HasResponse trait Saloon will not add the original response', function () {
    $mockClient = new MockClient([
        new MockResponse(['name' => 'Sammyjo20', 'actual_name' => 'Sam', 'twitter' => '@carre_sam']),
    ]);

    $response = connector()->send(new DTORequest, $mockClient);
    $dto = $response->dto();

    expect($dto)
        ->toBeInstanceOf(User::class)
        ->and($dto)->not->toBeInstanceOf(WithResponse::class);
});

test('if a dto implements the WithResponse interface and HasResponse trait Saloon will add the original response', function () {
    $mockClient = new MockClient([
        new MockResponse(['name' => 'Sammyjo20', 'actual_name' => 'Sam', 'twitter' => '@carre_sam']),
    ]);

    $request = new DTOWithResponseRequest();
    $response = connector()->send($request, $mockClient);

    /** @var UserWithResponse $dto */
    $dto = $response->dto();

    expect($dto)
        ->toBeInstanceOf(UserWithResponse::class)
        ->and($dto)->toBeInstanceOf(WithResponse::class)
        ->and($dto->getResponse())->toBe($response);
});
