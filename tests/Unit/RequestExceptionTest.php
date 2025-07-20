<?php

declare(strict_types=1);

use Salette\Exceptions\ClientException;
use Salette\Exceptions\RequestException;
use Salette\Exceptions\Statuses\ForbiddenException;
use Salette\Exceptions\Statuses\GatewayTimeoutException;
use Salette\Exceptions\Statuses\InternalServerErrorException;
use Salette\Exceptions\Statuses\MethodNotAllowedException;
use Salette\Exceptions\Statuses\NotFoundException;
use Salette\Exceptions\Statuses\PaymentRequiredException;
use Salette\Exceptions\Statuses\RequestTimeOutException;
use Salette\Exceptions\Statuses\ServiceUnavailableException;
use Salette\Exceptions\Statuses\TooManyRequestsException;
use Salette\Exceptions\Statuses\UnauthorizedException;
use Salette\Exceptions\Statuses\UnprocessableEntityException;
use Salette\Helpers\StatusCodeHelper;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\AlwaysHasFailureRequest;
use Salette\Tests\Fixtures\Requests\UserRequest;

test(
    'the response will return different exceptions based on status',
    function (int $status, string $expectedException) {
        $mockClient = new MockClient([
            MockResponse::make(['message' => 'Oh yee-naw!'], $status),
        ]);

        $response = TestConnector::make()->send(new UserRequest(), $mockClient);
        $exception = $response->toException();

        $message = sprintf('%s (%s) Response: %s', StatusCodeHelper::getMessage($status), $status, $response->body());

        expect($exception)
            ->toBeInstanceOf($expectedException)
            ->and($exception->getMessage())->toEqual($message);
    }
)->with([
    [401, UnauthorizedException::class],
    [402, PaymentRequiredException::class],
    [403, ForbiddenException::class],
    [404, NotFoundException::class],
    [405, MethodNotAllowedException::class],
    [408, RequestTimeOutException::class],
    [422, UnprocessableEntityException::class],
    [429, TooManyRequestsException::class],
    [500, InternalServerErrorException::class],
    [503, ServiceUnavailableException::class],
    [504, GatewayTimeoutException::class],
    [418, ClientException::class],
    [411, ClientException::class],
]);

test(
    'when the failed method is customised the response will return ok request exceptions',
    function (int $status, string $expectedException) {
        $mockClient = new MockClient([
            MockResponse::make(['message' => 'Oh yee-naw!'], $status),
        ]);

        $response = TestConnector::make()->send(new AlwaysHasFailureRequest(), $mockClient);
        $exception = $response->toException();

        $message = sprintf('%s (%s) Response: %s', StatusCodeHelper::getMessage($status), $status, $response->body());

        expect($exception)
            ->toBeInstanceOf($expectedException)
            ->and($exception->getMessage())->toEqual($message);
    }
)->with([
    [302, RequestException::class],
    [200, RequestException::class],
    [201, RequestException::class],
    [100, RequestException::class],
]);
