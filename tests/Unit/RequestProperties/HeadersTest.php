<?php

declare(strict_types=1);

use Salette\Repositories\ArrayStore;
use Salette\Tests\Fixtures\Requests\HeaderRequest;
use Salette\Tests\Fixtures\Connectors\HeaderConnector;

test('default headers are merged in from a request', function () {
    $request = new HeaderRequest();

    $headers = $request->headers();

    expect($headers)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($headers)->toEqual(new ArrayStore(['X-Custom-Header' => 'Howdy']));
});

test('headers can be managed on a request', function () {
    $request = new HeaderRequest();

    $headers = $request->headers()->add('Content-Type', 'custom/saloon');

    expect($headers)->toBeInstanceOf(ArrayStore::class);

    $headers =
        $request->headers()->merge(
            ['X-Merge-A' => 'Hello', 'Complex' => ['A', 'B']],
            ['X-Merge-B' => 'Goodbye', 'Content-Type' => 'overwritten']
        );

    expect($headers)->toBeInstanceOf(ArrayStore::class);

    $headers = $request->headers()->remove('X-Merge-B');

    expect($headers)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($headers->all())->toEqual([
            'X-Custom-Header' => 'Howdy',
            'Content-Type' => 'overwritten',
            'X-Merge-A' => 'Hello',
            'Complex' => ['A', 'B'],
        ])
        ->and($headers->get('X-Custom-Header'))->toEqual('Howdy')
        ->and($headers->get('Complex'))->toEqual(['A', 'B']);

    $headers = $request->headers()->set(['X-Different' => 'Yo']);

    expect($headers)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($request->headers()->all())->toEqual(['X-Different' => 'Yo'])
        ->and($request->headers()->isEmpty())->toBeFalse()
        ->and($request->headers()->isNotEmpty())->toBeTrue();
});

test('headers can be managed on a connector', function () {
    $connector = new HeaderConnector();

    $headers = $connector->headers()->add('Content-Type', 'custom/saloon');

    expect($headers)->toBeInstanceOf(ArrayStore::class);

    $headers =
        $connector->headers()->merge(
            ['X-Merge-A' => 'Hello', 'Complex' => ['A', 'B']],
            ['X-Merge-B' => 'Goodbye', 'Content-Type' => 'overwritten']
        );

    expect($headers)->toBeInstanceOf(ArrayStore::class);

    $headers = $connector->headers()->remove('X-Merge-B');

    expect($headers)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($headers->all())->toEqual([
            'X-Connector-Header' => 'Sam',
            'Content-Type' => 'overwritten',
            'X-Merge-A' => 'Hello',
            'Complex' => ['A', 'B'],
        ])
        ->and($headers->get('X-Connector-Header'))->toEqual('Sam')
        ->and($headers->get('Complex'))->toEqual(['A', 'B']);

    $headers = $connector->headers()->set(['X-Different' => 'Yo']);

    expect($headers)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($connector->headers()->all())->toEqual(['X-Different' => 'Yo'])
        ->and($connector->headers()->isEmpty())->toBeFalse()
        ->and($connector->headers()->isNotEmpty())->toBeTrue();
});
