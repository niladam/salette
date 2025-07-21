<?php

declare(strict_types=1);

use Salette\Senders\GuzzleSender;
use Salette\Tests\Fixtures\Senders\ArraySender;
use Salette\Tests\Fixtures\Requests\UserRequest;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Connectors\ArraySenderConnector;
use Salette\Tests\Fixtures\Connectors\ArraySenderDefaultMethodConnector;

test('the default sender on all connectors is the guzzle sender', function () {
    $connector = new TestConnector();
    $sender = $connector->sender();
    // Test the same instance is re-used
    expect($sender)
        ->toBeInstanceOf(GuzzleSender::class)
        ->and($connector->sender())->toBe($sender);
});

test('you can overwrite the sender on a connector using the property', function () {
    $connector = new ArraySenderConnector();
    $sender = $connector->sender();

    expect($sender)
        ->toBeInstanceOf(ArraySender::class)
        ->and($connector->sender())->toBe($sender);

    // Test using the connector with the custom sender

    $request = new UserRequest();
    $response = $connector->send($request);

    expect($response->headers()->all())
        ->toEqual(['X-Fake' => true])
        ->and($response->body())->toEqual('Default');
});

test('you can overwrite the sender on a connector using the defaultSender method', function () {
    $connector = new ArraySenderDefaultMethodConnector();
    $sender = $connector->sender();

    expect($sender)
        ->toBeInstanceOf(ArraySender::class)
        ->and($connector->sender())->toBe($sender);

    // Test using the connector with the custom sender

    $request = new UserRequest();
    $response = $connector->send($request);

    expect($response->headers()->all())
        ->toEqual(['X-Fake' => true])
        ->and($response->body())->toEqual('Default');
});

test('it will throw an exception if the sender does not implement the sender interface', function () {
    $connector = new ArraySenderConnector();
    $connector->setDefaultSender(UserRequest::class);

    $connector->sender();
})->throws(
    TypeError::class,
    'Return value of Salette\Tests\Fixtures\Connectors\ArraySenderConnector::defaultSender() must implement interface Salette\Contracts\Sender, instance of Salette\Tests\Fixtures\Requests\UserRequest returned'
);
