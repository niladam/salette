<?php

declare(strict_types=1);

use Salette\Repositories\ArrayStore;
use Salette\Tests\Fixtures\Requests\ConfigRequest;
use Salette\Tests\Fixtures\Connectors\ConfigConnector;

test('default config is merged in from a request', function () {
    $request = new ConfigRequest();

    $config = $request->config();

    expect($config)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($config)->toEqual(new ArrayStore(['debug' => false]));
});

test('config can be managed on a request', function () {
    $request = new ConfigRequest();

    $config = $request->config()->add('timeout', 60);

    expect($config)->toBeInstanceOf(ArrayStore::class);

    $config = $request->config()->merge(['name' => 'Sam', 'category' => 'Cowboy'], ['connect_timeout' => 200]);

    expect($config)->toBeInstanceOf(ArrayStore::class);

    $config = $request->config()->remove('category');

    expect($config)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($config->all())->toEqual([
            'timeout' => 60,
            'name' => 'Sam',
            'connect_timeout' => 200,
            'debug' => false,
        ])
        ->and($config->get('timeout'))->toEqual(60);

    $config = $request->config()->set(['debug' => true]);

    expect($config)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($request->config()->all())->toEqual(['debug' => true])
        ->and($request->config()->isEmpty())->toBeFalse()
        ->and($request->config()->isNotEmpty())->toBeTrue();
});

test('config can be managed on a connector', function () {
    $connector = new ConfigConnector();

    $config = $connector->config()->add('timeout', 60);

    expect($config)->toBeInstanceOf(ArrayStore::class);

    $config = $connector->config()->merge(['name' => 'Sam', 'category' => 'Cowboy'], ['connect_timeout' => 200]);

    expect($config)->toBeInstanceOf(ArrayStore::class);

    $config = $connector->config()->remove('category');

    expect($config)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($config->all())->toEqual([
            'timeout' => 60,
            'name' => 'Sam',
            'connect_timeout' => 200,
            'debug' => false,
        ])
        ->and($config->get('timeout'))->toEqual(60);

    $config = $connector->config()->set(['debug' => true]);

    expect($config)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($connector->config()->all())->toEqual(['debug' => true])
        ->and($connector->config()->isEmpty())->toBeFalse()
        ->and($connector->config()->isNotEmpty())->toBeTrue();
});
