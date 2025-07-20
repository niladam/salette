<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Uri;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Tests\Fixtures\Requests\ModifiedPsrUserRequest;
use Salette\Tests\Fixtures\Connectors\ModifiedPsrRequestConnector;

test('the connector and request can modify the psr request when it is created', function () {
    $mockClient = new MockClient([
        MockResponse::make(['name' => 'Sam']),
    ]);

    $connector = new ModifiedPsrRequestConnector;
    $connector->withMockClient($mockClient);

    $response = $connector->send(new ModifiedPsrUserRequest);

    // The connector will change the URI to https://google.com

    expect($response->getPsrRequest()->getUri())
        ->toEqual(new Uri('https://google.com'))
        ->and($response->getPsrRequest()->getHeaders())->toHaveKey('X-Howdy', ['Yeehaw']);

    // The request will add the X-Howdy header

});
