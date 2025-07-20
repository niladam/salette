<?php

declare(strict_types=1);

use Salette\Repositories\ArrayStore;
use Salette\Tests\Fixtures\Requests\QueryParameterRequest;
use Salette\Tests\Fixtures\Connectors\QueryParameterConnector;

test('default query parameters are merged in from a request', function () {
    $request = new QueryParameterRequest();

    $query = $request->query();

    expect($query)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($query)->toEqual(new ArrayStore(['per_page' => 100]));
});

test('query parameters can be managed on a request', function () {
    $request = new QueryParameterRequest();

    $query = $request->query()->add('page', 1);

    expect($query)->toBeInstanceOf(ArrayStore::class);

    $query = $request->query()->merge(['search' => 'Sam', 'category' => 'Cowboy'], ['per_page' => 200]);

    expect($query)->toBeInstanceOf(ArrayStore::class);

    $query = $request->query()->remove('category');

    expect($query)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($query->all())->toEqual([
            'per_page' => 200,
            'page' => 1,
            'search' => 'Sam',
        ])
        ->and($query->get('page'))->toEqual(1);

    $query = $request->query()->set(['debug' => true]);

    expect($query)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($request->query()->all())->toEqual(['debug' => true])
        ->and($request->query()->isEmpty())->toBeFalse()
        ->and($request->query()->isNotEmpty())->toBeTrue();
});

test('query parameters can be managed on a connector', function () {
    $connector = new QueryParameterConnector();

    $query = $connector->query()->add('page', 1);

    expect($query)->toBeInstanceOf(ArrayStore::class);

    $query = $connector->query()->merge(['search' => 'Sam', 'category' => 'Cowboy'], ['sort' => 'last_name']);

    expect($query)->toBeInstanceOf(ArrayStore::class);

    $query = $connector->query()->remove('category');

    expect($query)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($query->all())->toEqual([
            'sort' => 'last_name',
            'page' => 1,
            'search' => 'Sam',
        ])
        ->and($query->get('page'))->toEqual(1);

    $query = $connector->query()->set(['debug' => true]);

    expect($query)
        ->toBeInstanceOf(ArrayStore::class)
        ->and($connector->query()->all())->toEqual(['debug' => true])
        ->and($connector->query()->isEmpty())->toBeFalse()
        ->and($connector->query()->isNotEmpty())->toBeTrue();
});
