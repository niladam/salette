<?php

declare(strict_types=1);

use Salette\Enums\Method;
use Salette\Exceptions\InvalidHeaderException;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Requests\PendingRequest;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\UserRequest;

test('you can overwrite the url and the method of the pending request', function () {
    $connector = new TestConnector();

    $connector->withMockClient(new MockClient([
        new MockResponse(['name' => 'Sam']),
    ]));

    $connector->middleware()->onRequest(function (PendingRequest $pendingRequest) {
        $pendingRequest->setUrl('https://other-endpoint.co.uk' . $pendingRequest->getRequest()->resolveEndpoint());
        $pendingRequest->setMethod(Method::POST);
    });

    $request = new UserRequest();

    expect($request->getMethod())->toEqual(Method::GET);

    $response = $connector->send(new UserRequest());
    $pendingRequest = $response->getPendingRequest();

    expect($pendingRequest->getUrl())
        ->toEqual('https://other-endpoint.co.uk/user')
        ->and($pendingRequest->getMethod())->toEqual(Method::POST);
});

test('the pending request is macroable', function () {
    PendingRequest::macro('yee', fn () => 'haw');

    $pendingRequest = connector()->createPendingRequest(new UserRequest());

    expect($pendingRequest->yee())->toEqual('haw');
});

test('the pending request validates properly formed headers', function () {
    $request = new UserRequest();

    $request->headers()->set([
        'Content-Type: application/json',
    ]);

    $this->expectException(InvalidHeaderException::class);
    $this->expectExceptionMessage(
        'One or more of the headers are invalid. Make sure to use the header name as the key. 
        For example: [\'Content-Type\' => \'application/json\'].'
    );

    connector()->createPendingRequest($request);
});
