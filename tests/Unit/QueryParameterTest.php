<?php

declare(strict_types=1);

use Salette\Tests\Fixtures\Requests\UserRequest;
use Salette\Tests\Fixtures\Requests\QueryParameterRequest;
use Salette\Tests\Fixtures\Connectors\QueryParameterConnector;
use Salette\Tests\Fixtures\Requests\QueryParameterConnectorRequest;
use Salette\Tests\Fixtures\Requests\QueryParameterConnectorBlankRequest;
use Salette\Tests\Fixtures\Requests\OverwrittenQueryParameterConnectorRequest;

test('a request with query params added sends the query params', function () {
    $request = new QueryParameterRequest();

    $request->query()->add('sort', '-created_at');

    $pendingRequest = connector()->createPendingRequest($request);

    $query = $pendingRequest->query()->all();

    expect($query)
        ->toHaveKey('per_page', 100)
        ->and($query)->toHaveKey('sort', '-created_at'); // Test Default
    // Test Adding Data After
});

test('if setQuery is used, all other default query params wont be included', function () {
    $request = new QueryParameterConnectorRequest();

    $request->query()->set([
        'sort' => 'nickname',
    ]);

    $pendingRequest = connector()->createPendingRequest($request);
    $query = $pendingRequest->query()->all();

    expect($query)->toEqual(['sort' => 'nickname']);
});

test('a connector can have query that is set', function () {
    $request = new QueryParameterConnectorRequest();

    $pendingRequest = (new QueryParameterConnector)->createPendingRequest($request);
    $query = $pendingRequest->query()->all();

    expect($query)
        ->toHaveKey('sort', 'first_name')
        ->and($query)->toHaveKey('include', 'user'); // Added by connector
    // Added by request
});

test('a request query parameter can overwrite a connectors parameter', function () {
    $request = new OverwrittenQueryParameterConnectorRequest();

    $pendingRequest = connector()->createPendingRequest($request);
    $query = $pendingRequest->query()->all();

    expect($query)->toEqual(['sort' => 'date_of_birth']);
});

test('manually overwriting query parameter in runtime can overwrite connector parameter', function () {
    $request = new QueryParameterConnectorBlankRequest();

    $request->query()->add('sort', 'custom_field');

    $pendingRequest = connector()->createPendingRequest($request);
    $query = $pendingRequest->query()->all();

    expect($query)->toEqual(['sort' => 'custom_field']);
});

test('manually overwriting query parameter in runtime can overwrite request parameter', function () {
    $request = new QueryParameterRequest();

    $request->query()->add('per_page', 500);

    $pendingRequest = connector()->createPendingRequest($request);
    $query = $pendingRequest->query()->all();

    expect($query)->toEqual(['per_page' => 500]);
});

test('when not sending query parameters, the query option is not set', function () {
    $request = new UserRequest();

    $pendingRequest = connector()->createPendingRequest($request);
    $query = $pendingRequest->query()->all();

    expect($query)->toBeEmpty();
});
