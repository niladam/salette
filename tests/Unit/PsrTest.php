<?php

declare(strict_types=1);

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Salette\Tests\Fixtures\Connectors\QueryParameterConnector;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\HasJsonBodyRequest;
use Salette\Tests\Fixtures\Requests\QueryParameterRequest;
use Salette\Tests\Fixtures\Requests\UserRequest;

test('a psr-7 request can be created from the PendingRequest', function () {
    $connector = new TestConnector();
    $request = new UserRequest();

    $pendingRequest = $connector->createPendingRequest($request);
    $request = $pendingRequest->createPsrRequest();

    expect($request)
        ->toBeInstanceOf(RequestInterface::class)
        ->and($request->getUri())->toBeInstanceOf(UriInterface::class)
        ->and((string) $request->getUri())->toEqual('https://tests.saloon.dev/api/user')
        ->and($request->getMethod())->toEqual('GET')
        ->and($request->getHeaders())->toEqual([
            'Host' => ['tests.saloon.dev'],
            'Accept' => ['application/json'],
        ])
        ->and($request->getProtocolVersion())->toEqual('1.1');
});

test('if request body is present then it will be on the psr-7 request', function () {
    $connector = new TestConnector();
    $request = new HasJsonBodyRequest();

    $pendingRequest = $connector->createPendingRequest($request);
    $request = $pendingRequest->createPsrRequest();

    $body = $request->getBody();

    expect($body)
        ->toBeInstanceOf(StreamInterface::class)
        ->and($body->getContents())->toEqual('{"name":"Sam","catchphrase":"Yeehaw!"}');
});

test('you can generate a uri from the PendingRequest', function () {
    $connector = new QueryParameterConnector();
    $request = new QueryParameterRequest('/user?include=hats#fragment-123');

    $pendingRequest = $connector->createPendingRequest($request);
    $uri = $pendingRequest->getUri();

    expect($uri)
        ->toBeInstanceOf(UriInterface::class)
        ->and((string) $uri)->toEqual(
            'https://tests.saloon.dev/api/user?include=hats&sort=first_name&per_page=100#fragment-123'
        )
        ->and($uri->getScheme())->toEqual('https')
        ->and($uri->getHost())->toEqual('tests.saloon.dev')
        ->and($uri->getPath())->toEqual('/api/user')
        ->and($uri->getQuery())->toEqual('include=hats&sort=first_name&per_page=100')
        ->and($uri->getFragment())->toEqual('fragment-123');
});

test('when using the url for query parameters you can use dots and value-less parameters', function () {
    $connector = new TestConnector();
    $request = new QueryParameterRequest('/user?account.id=1&checked&name=sam');

    $pendingRequest = $connector->createPendingRequest($request);
    $uri = $pendingRequest->getUri();

    expect($uri->getQuery())->toEqual('account.id=1&checked=&name=sam&per_page=100');
});
