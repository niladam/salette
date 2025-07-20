<?php

declare(strict_types=1);

use Salette\Http\Response;
use Salette\Requests\PendingRequest;
use Salette\Senders\GuzzleSender;
use Salette\Tests\Fixtures\Requests\UserRequest;
use Salette\Tests\Fixtures\Requests\ErrorRequest;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\HasConnectorUserRequest;

test('a request can be made successfully', function () {
    $connector = new TestConnector();
    $response = $connector->send(new UserRequest);

    $data = $response->json();

    expect($response->getPendingRequest()->isAsynchronous())
        ->toBeFalse()
        ->and($response)->toBeInstanceOf(Response::class)
        ->and($response->isMocked())->toBeFalse()
        ->and($response->status())->toEqual(200)
        ->and($data)->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);
});

test('a request can handle an exception properly', function () {
    $connector = new TestConnector();
    $response = $connector->send(new ErrorRequest);

    expect($response->isMocked())
        ->toBeFalse()
        ->and($response->status())->toEqual(500);
});

test('a request with HasConnector can be sent individually', function () {
    $request = new HasConnectorUserRequest();

    expect($request->connector())
        ->toBeInstanceOf(TestConnector::class)
        ->and($request->sender())->toBeInstanceOf(GuzzleSender::class)
        ->and($request->createPendingRequest())->toBeInstanceOf(PendingRequest::class);

    $response = $request->send();

    $data = $response->json();

    expect($response)
        ->toBeInstanceOf(Response::class)
        ->and($response->isMocked())->toBeFalse()
        ->and($response->status())->toEqual(200)
        ->and($data)->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);
});
