<?php

declare(strict_types=1);

use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise\PromiseInterface;
use Salette\Exceptions\ClientException;
use Salette\Exceptions\FatalRequestException;
use Salette\Exceptions\RequestException;
use Salette\Exceptions\ServerException as SaloonServerException;
use Salette\Exceptions\Statuses\InternalServerErrorException;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Http\Response;
use Salette\Requests\PendingRequest;
use Salette\Tests\Fixtures\Connectors\BadResponseConnector;
use Salette\Tests\Fixtures\Connectors\CustomExceptionConnector;
use Salette\Tests\Fixtures\Connectors\CustomFailHandlerConnector;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Exceptions\ConnectorRequestException;
use Salette\Tests\Fixtures\Exceptions\CustomRequestException;
use Salette\Tests\Fixtures\Requests\BadResponseRequest;
use Salette\Tests\Fixtures\Requests\CustomExceptionUserRequest;
use Salette\Tests\Fixtures\Requests\CustomFailHandlerRequest;
use Salette\Tests\Fixtures\Requests\ErrorRequest;
use Salette\Tests\Fixtures\Requests\NotFoundFailedRequest;
use Salette\Tests\Fixtures\Requests\UserRequest;

test('you can use the to exception method to get the default RequestException exception with GuzzleSender', function () {
    $response = TestConnector::make()->send(new ErrorRequest());

    expect($response)->toBeInstanceOf(Response::class);

    $exception = $response->toException();

    expect($exception)
        ->toBeInstanceOf(InternalServerErrorException::class)
        ->and($exception)->toBeInstanceOf(SaloonServerException::class)
        ->and($exception->getMessage())->toEqual('Internal Server Error (500) Response: ' . $response->body())
        ->and($exception->getPrevious())->toBeInstanceOf(ServerException::class);

    $this->expectExceptionObject($exception);

    $response->throw();
});

test('you can use the to exception method to get the default RequestException exception', function () {
    $mockClient = new MockClient([
        MockResponse::make(['message' => 'Server Error'], 500),
    ]);

    $response = TestConnector::make()->send(new UserRequest(), $mockClient);

    expect($response)->toBeInstanceOf(Response::class);

    $exception = $response->toException();

    expect($exception)
        ->toBeInstanceOf(InternalServerErrorException::class)
        ->and($exception)->toBeInstanceOf(SaloonServerException::class)
        ->and($exception->getMessage())->toEqual('Internal Server Error (500) Response: ' . $response->body())
        ->and($exception->getPrevious())->toEqual(null);

    // Previous is null with the SimulatedSender

    $this->expectExceptionObject($exception);

    $response->throw();
});

test('it throws exceptions properly with promises with GuzzleSender', function () {
    $promise = TestConnector::make()->sendAsync(new ErrorRequest());

    $correctInstance = false;

    $promise->otherwise(function (Throwable $exception) use (&$correctInstance) {
        if ($exception instanceof RequestException) {
            $correctInstance = true;
        }
    });

    try {
        $promise->wait();
    } catch (Throwable $exception) {
        expect($correctInstance)
            ->toBeTrue()
            ->and($exception)->toBeInstanceOf(RequestException::class)
            ->and($exception->getResponse())->toBeInstanceOf(Response::class)
            ->and($exception->getMessage())->toEqual(
                'Internal Server Error (500) Response: ' . $exception->getResponse()->body()
            )
            ->and($exception->getPrevious())->toBeInstanceOf(ServerException::class);
    }
});

test('it throws exceptions properly with promises', function () {
    $mockClient = new MockClient([
        MockResponse::make(['message' => 'Bad Request'], 422),
    ]);

    $promise = TestConnector::make()->sendAsync(new ErrorRequest(), $mockClient);

    try {
        $promise->wait();
    } catch (Throwable $exception) {
        expect($exception)
            ->toBeInstanceOf(ClientException::class)
            ->and($exception->getResponse())->toBeInstanceOf(Response::class)
            ->and($exception->getMessage())->toEqual(
                'Unprocessable Entity (422) Response: ' . $exception->getResponse()->body()
            )
            ->and($exception->getPrevious())->toBeNull();
    }
});

test('you can customise the exception handler on a connector', function () {
    $mockClient = new MockClient([
        MockResponse::make(['message' => 'Server Error'], 500),
    ]);

    $response = CustomExceptionConnector::make()->send(new UserRequest(), $mockClient);
    $exception = $response->toException();

    expect($exception)
        ->toBeInstanceOf(ConnectorRequestException::class)
        ->and($exception->getMessage())->toEqual('Oh yee-naw.');
});

test('you can customise the exception handler on a request', function () {
    $mockClient = new MockClient([
        MockResponse::make(['message' => 'Server Error'], 500),
    ]);

    $response = TestConnector::make()->send(new CustomExceptionUserRequest(), $mockClient);
    $exception = $response->toException();

    expect($exception)
        ->toBeInstanceOf(CustomRequestException::class)
        ->and($exception->getMessage())->toEqual('Oh yee-naw.');
});

test('the request exception handler will always take priority', function () {
    $mockClient = new MockClient([
        MockResponse::make(['message' => 'Server Error'], 500),
    ]);

    $response = CustomExceptionConnector::make()->send(new CustomExceptionUserRequest(), $mockClient);
    $exception = $response->toException();

    expect($exception)
        ->toBeInstanceOf(CustomRequestException::class)
        ->and($exception->getMessage())->toEqual('Oh yee-naw.');
});

test('you can customise if saloon should throw an exception on a connector', function () {
    $mockClient = new MockClient([
        MockResponse::make(['message' => 'Success']),
        MockResponse::make(['message' => 'Error: Invalid Cowboy Hat']),
    ]);

    $responseA = BadResponseConnector::make()->send(new UserRequest(), $mockClient);

    expect($responseA->shouldThrowRequestException())
        ->toBeFalse()
        ->and($responseA->toException())->toBeNull();

    $responseB = BadResponseConnector::make()->send(new UserRequest(), $mockClient);
    expect($responseB->shouldThrowRequestException())->toBeTrue();
    $exceptionB = $responseB->toException();

    expect($exceptionB)
        ->toBeInstanceOf(RequestException::class)
        ->and($exceptionB->getPendingRequest())->toBeInstanceOf(PendingRequest::class)
        ->and($exceptionB->getResponse())->toBeInstanceOf(Response::class)
        ->and($exceptionB->getMessage())->toEqual('OK (200) Response: ' . $exceptionB->getResponse()->body())
        ->and($exceptionB->getPrevious())->toBeNull();
});

test('you can customise if saloon should throw an exception on a request', function () {
    $mockClient = new MockClient([
        MockResponse::make(['message' => 'Success']),
        MockResponse::make(['message' => 'Yee-naw: Horse Not Found']),
    ]);

    $responseA = TestConnector::make()->send(new BadResponseRequest(), $mockClient);

    expect($responseA->shouldThrowRequestException())
        ->toBeFalse()
        ->and($responseA->toException())->toBeNull();

    $responseB = TestConnector::make()->send(new BadResponseRequest(), $mockClient);
    expect($responseB->shouldThrowRequestException())->toBeTrue();
    $exceptionB = $responseB->toException();

    expect($exceptionB)
        ->toBeInstanceOf(RequestException::class)
        ->and($exceptionB->getPendingRequest())->toBeInstanceOf(PendingRequest::class)
        ->and($exceptionB->getResponse())->toBeInstanceOf(Response::class)
        ->and($exceptionB->getMessage())->toEqual('OK (200) Response: ' . $exceptionB->getResponse()->body())
        ->and($exceptionB->getPrevious())->toBeNull();
});

test('when both the connector and request have custom logic to determine different failures they work together', function () {
    $mockClient = new MockClient([
        MockResponse::make(['message' => 'Success']),
        MockResponse::make(['message' => 'Error: Invalid Cowboy Hat']),
        MockResponse::make(['message' => 'Yee-naw: Horse Not Found']),
    ]);

    $responseA = BadResponseConnector::make()->send(new BadResponseRequest(), $mockClient);

    expect($responseA->shouldThrowRequestException())
        ->toBeFalse()
        ->and($responseA->toException())->toBeNull();

    $responseB = BadResponseConnector::make()->send(new BadResponseRequest(), $mockClient);
    expect($responseB->shouldThrowRequestException())->toBeTrue();
    $exceptionB = $responseB->toException();

    expect($exceptionB)
        ->toBeInstanceOf(RequestException::class)
        ->and($exceptionB->getPendingRequest())->toBeInstanceOf(PendingRequest::class)
        ->and($exceptionB->getResponse())->toBeInstanceOf(Response::class)
        ->and($exceptionB->getMessage())->toEqual('OK (200) Response: ' . $exceptionB->getResponse()->body())
        ->and($exceptionB->getPrevious())->toBeNull();

    $responseC = BadResponseConnector::make()->send(new BadResponseRequest(), $mockClient);
    expect($responseC->shouldThrowRequestException())->toBeTrue();
    $exceptionC = $responseC->toException();

    expect($exceptionC)
        ->toBeInstanceOf(RequestException::class)
        ->and($exceptionC->getResponse())->toBeInstanceOf(Response::class)
        ->and($exceptionC->getMessage())->toEqual('OK (200) Response: ' . $exceptionC->getResponse()->body())
        ->and($exceptionC->getPrevious())->toBeNull();
});

test('you can customise if saloon determines if a request has failed on a connector', function () {
    $mockClient = new MockClient([
        MockResponse::make(['message' => 'Success']),
        MockResponse::make(['message' => 'Error: Invalid Cowboy Hat']),
    ]);

    $responseA = CustomFailHandlerConnector::make()->send(new UserRequest(), $mockClient);

    expect($responseA->failed())->toBeFalse();

    $responseB = CustomFailHandlerConnector::make()->send(new UserRequest(), $mockClient);

    expect($responseB->failed())->toBeTrue();
});

test('you can customise if saloon determines if a request has failed on a request', function () {
    $mockClient = new MockClient([
        MockResponse::make(['message' => 'Success']),
        MockResponse::make(['message' => 'Yee-naw: Horse Not Found']),
    ]);

    $responseA = TestConnector::make()->send(new CustomFailHandlerRequest(), $mockClient);

    expect($responseA->failed())->toBeFalse();

    $responseB = TestConnector::make()->send(new CustomFailHandlerRequest(), $mockClient);

    expect($responseB->failed())->toBeTrue();
});

test('a request can mark a request as not failed', function () {
    $response = TestConnector::make()->send(new NotFoundFailedRequest());

    expect($response->failed())->toBeFalse();
});

test('a request can mark a request as not failed with asynchronous requests', function () {
    $response = TestConnector::make()->sendAsync(new NotFoundFailedRequest())->wait();

    expect($response->failed())->toBeFalse();
});

test('a request can mark a request as not failed with pools', function () {
    $responseCount = 0;
    $exceptionCount = 0;

    $pool = TestConnector::make()->pool([
        new NotFoundFailedRequest(),
    ]);

    $pool->withResponseHandler(function (Response $response) use (&$responseCount) {
        expect($response)
            ->toBeInstanceOf(Response::class)
            ->and($response->status())->toBe(404);

        $responseCount++;
    })->withExceptionHandler(function (RequestException $exception) use (&$exceptionCount) {
        $response = $exception->getResponse();

        expect($response)
            ->toBeInstanceOf(Response::class)
            ->and($response->status())->toBe(404);

        $exceptionCount++;
    });

    $promise = $pool->send();

    expect($promise)->toBeInstanceOf(PromiseInterface::class);

    $promise->wait();

    expect($responseCount)
        ->toEqual(1)
        ->and($exceptionCount)->toEqual(0);
});

test('the sender will throw a FatalRequestException if it cannot connect to a site using synchronous', function (string $url) {
    $connector = new TestConnector($url);
    $request = new UserRequest();

    $this->expectException(FatalRequestException::class);

    $connector->send($request);
})->with([
    'https://saloon.saloon.test',
    'https://saloon.doesnt-exist',
]);

test('the sender will throw a FatalRequestException if it cannot connect to a site using asynchronous', function (string $url) {
    $connector = new TestConnector($url);
    $request = new UserRequest();

    $this->expectException(FatalRequestException::class);

    $connector->sendAsync($request)->wait();
})->with([
    'https://saloon.saloon.test',
    'https://saloon.doesnt-exist',
]);
