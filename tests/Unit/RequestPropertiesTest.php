<?php

declare(strict_types=1);

use Salette\Helpers\MiddlewarePipeline;
use Salette\Repositories\ArrayStore;
use Salette\Tests\Fixtures\Requests\DefaultPropertiesRequest;
use Salette\Tests\Fixtures\Requests\UserRequest;

test('you can retrieve all the request parameters methods', function () {
    $request = new UserRequest();

    expect($request->headers())
        ->toBeInstanceOf(ArrayStore::class)
        ->and($request->query())->toBeInstanceOf(ArrayStore::class)
        ->and($request->config())->toBeInstanceOf(ArrayStore::class)
        ->and($request->middleware())->toBeInstanceOf(MiddlewarePipeline::class);
});

test('all of the request properties can have default properties', function () {
    $request = new DefaultPropertiesRequest();

    expect($request->headers())
        ->toEqual(new ArrayStore(['X-Favourite-Artist' => 'Luke Combs']))
        ->and($request->query())->toEqual(new ArrayStore(['format' => 'json']))
        ->and($request->config())->toEqual(new ArrayStore(['debug' => true]));
});
