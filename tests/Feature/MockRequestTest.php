<?php

declare(strict_types=1);

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Salette\Exceptions\FixtureException;
use Salette\Exceptions\NoMockResponseFoundException;
use Salette\Exceptions\RequestException;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Http\Response;
use Salette\MockConfig;
use Salette\Requests\PendingRequest;
use Salette\Tests\Fixtures\Connectors\DifferentServiceConnector;
use Salette\Tests\Fixtures\Connectors\QueryParameterConnector;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Mocking\BeforeSaveUserFixture;
use Salette\Tests\Fixtures\Mocking\CallableMockResponse;
use Salette\Tests\Fixtures\Mocking\MissingNameFixture;
use Salette\Tests\Fixtures\Mocking\RegexUserFixture;
use Salette\Tests\Fixtures\Mocking\SafeUserFixture;
use Salette\Tests\Fixtures\Mocking\SuperheroFixture;
use Salette\Tests\Fixtures\Mocking\UserFixture;
use Salette\Tests\Fixtures\Requests\AlwaysThrowRequest;
use Salette\Tests\Fixtures\Requests\DifferentServiceUserRequest;
use Salette\Tests\Fixtures\Requests\ErrorRequest;
use Salette\Tests\Fixtures\Requests\FileDownloadRequest;
use Salette\Tests\Fixtures\Requests\PagedSuperheroRequest;
use Salette\Tests\Fixtures\Requests\QueryParameterConnectorRequest;
use Salette\Tests\Fixtures\Requests\UserRequest;

$filesystem = new Filesystem(new LocalFilesystemAdapter('tests/Fixtures/Saloon/Testing'));

beforeEach(function () use ($filesystem) {
    MockConfig::setFixturePath('tests/Fixtures/Saloon/Testing');

    $filesystem->deleteDirectory('/');
    $filesystem->createDirectory('/');
});

afterEach(function () {
    MockConfig::setFixturePath('tests/Fixtures/Saloon');
});

test('a request can be mocked with a sequence', function () {
    $mockClient = new MockClient([
        MockResponse::make(['name' => 'Sam'], 200, ['X-Foo' => 'Bar']),
        MockResponse::make(['name' => 'Alex']),
        MockResponse::make(['error' => 'Server Unavailable'], 500),
    ]);

    $connector = new TestConnector();
    $connector->withMockClient($mockClient);

    $responseA = $connector->send(new UserRequest());

    expect($responseA)
        ->toBeInstanceOf(Response::class)
        ->and($responseA->isMocked())->toBeTrue()
        ->and($responseA->isFaked())->toBeTrue()
        ->and($responseA->isCached())->toBeFalse()
        ->and($responseA->json())->toEqual(['name' => 'Sam'])
        ->and($responseA->status())->toEqual(200)
        ->and($responseA->getFakeResponse())->toBeInstanceOf(MockResponse::class)
        ->and($responseA->headers()->all())->toEqual(['X-Foo' => 'Bar']);

    $responseB = $connector->send(new UserRequest());

    expect($responseB)
        ->toBeInstanceOf(Response::class)
        ->and($responseB->isMocked())->toBeTrue()
        ->and($responseB->isFaked())->toBeTrue()
        ->and($responseB->isCached())->toBeFalse()
        ->and($responseB->json())->toEqual(['name' => 'Alex'])
        ->and($responseB->status())->toEqual(200)
        ->and($responseB->getFakeResponse())->toBeInstanceOf(MockResponse::class);

    $responseC = $connector->send(new UserRequest());

    expect($responseC)
        ->toBeInstanceOf(Response::class)
        ->and($responseC->isMocked())->toBeTrue()
        ->and($responseC->isFaked())->toBeTrue()
        ->and($responseC->isCached())->toBeFalse()
        ->and($responseC->json())->toEqual(['error' => 'Server Unavailable'])
        ->and($responseC->status())->toEqual(500)
        ->and($responseC->getFakeResponse())->toBeInstanceOf(MockResponse::class);

    $this->expectException(NoMockResponseFoundException::class);
    $this->expectExceptionMessage(
        'Salette was unable to guess a mock response for your request [https://tests.saloon.dev/api/user]'
    );

    $connector->send(new UserRequest());
});

test('a request can be mocked with a connector defined', function () {
    $responseA = MockResponse::make(['name' => 'Sammyjo20']);
    $responseB = MockResponse::make(['name' => 'Alex']);

    $connectorA = new TestConnector();
    $connectorB = new QueryParameterConnector();

    $connectorARequest = new UserRequest();
    $connectorBRequest = new QueryParameterConnectorRequest();

    $mockClient = new MockClient([
        TestConnector::class => $responseA,
        QueryParameterConnector::class => $responseB,
    ]);

    $responseA = $connectorA->send($connectorARequest, $mockClient);

    expect($responseA->isMocked())
        ->toBeTrue()
        ->and($responseA->json())->toEqual(['name' => 'Sammyjo20'])
        ->and($responseA->status())->toEqual(200);

    $responseB = $connectorB->send($connectorBRequest, $mockClient);

    expect($responseB->isMocked())
        ->toBeTrue()
        ->and($responseB->json())->toEqual(['name' => 'Alex'])
        ->and($responseB->status())->toEqual(200);
});

test('a request can be mocked with a request defined', function () {
    $responseA = MockResponse::make(['name' => 'Sammyjo20']);
    $responseB = MockResponse::make(['name' => 'Alex']);

    $connectorA = new TestConnector();
    $connectorB = new QueryParameterConnector();

    $requestA = new UserRequest();
    $requestB = new QueryParameterConnectorRequest();

    $mockClient = new MockClient([
        UserRequest::class => $responseA,
        QueryParameterConnectorRequest::class => $responseB,
    ]);

    $responseA = $connectorA->send($requestA, $mockClient);

    expect($responseA->isMocked())
        ->toBeTrue()
        ->and($responseA->json())->toEqual(['name' => 'Sammyjo20'])
        ->and($responseA->status())->toEqual(200);

    $responseB = $connectorB->send($requestB, $mockClient);

    expect($responseB->isMocked())
        ->toBeTrue()
        ->and($responseB->json())->toEqual(['name' => 'Alex'])
        ->and($responseB->status())->toEqual(200);
});

test('a request can be mocked with a url defined', function () {
    $responseA = MockResponse::make(['name' => 'Sammyjo20']);
    $responseB = MockResponse::make(['name' => 'Alex']);
    $responseC = MockResponse::make(['error' => 'Server Broken'], 500);

    $connectorA = new TestConnector();
    $connectorB = new DifferentServiceConnector();

    $requestA = new UserRequest();
    $requestB = new ErrorRequest();
    $requestC = new DifferentServiceUserRequest();

    $mockClient = new MockClient([
        'tests.saloon.dev/api/user' => $responseA, // Test Exact Route
        'tests.saloon.dev/*' => $responseB, // Test Wildcard Routes
        'google.com/*' => $responseC, // Test Different Route,
    ]);

    $responseA = $connectorA->send($requestA, $mockClient);

    expect($responseA->isMocked())
        ->toBeTrue()
        ->and($responseA->json())->toEqual(['name' => 'Sammyjo20'])
        ->and($responseA->status())->toEqual(200);

    $responseB = $connectorA->send($requestB, $mockClient);

    expect($responseB->isMocked())
        ->toBeTrue()
        ->and($responseB->json())->toEqual(['name' => 'Alex'])
        ->and($responseB->status())->toEqual(200);

    $responseC = $connectorB->send($requestC, $mockClient);

    expect($responseC->isMocked())
        ->toBeTrue()
        ->and($responseC->json())->toEqual(['error' => 'Server Broken'])
        ->and($responseC->status())->toEqual(500);
});

test('you can create wildcard url mocks', function () {
    $responseA = MockResponse::make(['name' => 'Sammyjo20']);
    $responseB = MockResponse::make(['name' => 'Alex']);
    $responseC = MockResponse::make(['error' => 'Server Broken'], 500);

    $connectorA = new TestConnector();
    $connectorB = new DifferentServiceConnector();

    $requestA = new UserRequest();
    $requestB = new ErrorRequest();
    $requestC = new DifferentServiceUserRequest();

    $mockClient = new MockClient([
        'tests.saloon.dev/api/user' => $responseA, // Test Exact Route
        'tests.saloon.dev/*' => $responseB, // Test Wildcard Routes
        '*' => $responseC,
    ]);

    $responseA = $connectorA->send($requestA, $mockClient);

    expect($responseA->isMocked())
        ->toBeTrue()
        ->and($responseA->json())->toEqual(['name' => 'Sammyjo20'])
        ->and($responseA->status())->toEqual(200);

    $responseB = $connectorA->send($requestB, $mockClient);

    expect($responseB->isMocked())
        ->toBeTrue()
        ->and($responseB->json())->toEqual(['name' => 'Alex'])
        ->and($responseB->status())->toEqual(200);

    $responseC = $connectorB->send($requestC, $mockClient);

    expect($responseC->isMocked())
        ->toBeTrue()
        ->and($responseC->json())->toEqual(['error' => 'Server Broken'])
        ->and($responseC->status())->toEqual(500);
});

test('you can use a closure for the mock response', function () {
    $sequenceMock = new MockClient([
        function (PendingRequest $pendingRequest) {
            return new MockResponse(['request' => $pendingRequest->getUrl()]);
        },
    ]);

    $sequenceResponse = connector()->send(new UserRequest(), $sequenceMock);

    expect($sequenceResponse->isMocked())
        ->toBeTrue()
        ->and($sequenceResponse->json())->toEqual(['request' => 'https://tests.saloon.dev/api/user']);

    // Connector mock

    $connectorMock = new MockClient([
        TestConnector::class => function (PendingRequest $pendingRequest) {
            return new MockResponse(['request' => $pendingRequest->getUrl()]);
        },
    ]);

    $connectorResponse = connector()->send(new UserRequest(), $connectorMock);

    expect($connectorResponse->isMocked())
        ->toBeTrue()
        ->and($connectorResponse->json())->toEqual(['request' => 'https://tests.saloon.dev/api/user']);

    // Request mock

    $requestMock = new MockClient([
        UserRequest::class => function (PendingRequest $pendingRequest) {
            return new MockResponse(['request' => $pendingRequest->getUrl()]);
        },
    ]);

    $requestResponse = connector()->send(new UserRequest(), $requestMock);

    expect($requestResponse->isMocked())
        ->toBeTrue()
        ->and($requestResponse->json())->toEqual(['request' => 'https://tests.saloon.dev/api/user']);

    // URL mock

    $urlMock = new MockClient([
        'tests.saloon.dev/*' => function (PendingRequest $pendingRequest) {
            return new MockResponse(['request' => $pendingRequest->getUrl()]);
        },
    ]);

    $urlResponse = connector()->send(new UserRequest(), $urlMock);

    expect($urlResponse->isMocked())
        ->toBeTrue()
        ->and($urlResponse->json())->toEqual(['request' => 'https://tests.saloon.dev/api/user']);
});

test('you can use a callable class as the mock response', function () {
    $mockClient = new MockClient([
        UserRequest::class => new CallableMockResponse(),
    ]);

    $sequenceResponse = connector()->send(new UserRequest(), $mockClient);

    expect($sequenceResponse->isMocked())
        ->toBeTrue()
        ->and($sequenceResponse->json())->toEqual(['request_class' => UserRequest::class]);
});

test('a fixture can be used with a mock sequence', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('user'),
        MockResponse::fixture('user'),
    ]);

    $responseA = connector()->send(new UserRequest(), $mockClient);

    expect($responseA->isMocked())
        ->toBeFalse()
        ->and($responseA->status())->toEqual(200)
        ->and($responseA->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);

    $responseB = connector()->send(new UserRequest(), $mockClient);

    expect($responseB->isMocked())
        ->toBeTrue()
        ->and($responseB->status())->toEqual(200)
        ->and($responseB->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);
});

test('a fixture can be used with a connector mock', function () {
    $mockClient = new MockClient([
        TestConnector::class => MockResponse::fixture('connector'),
    ]);

    $responseA = connector()->send(new UserRequest(), $mockClient);

    expect($responseA->isMocked())
        ->toBeFalse()
        ->and($responseA->status())->toEqual(200)
        ->and($responseA->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);

    $responseB = connector()->send(new UserRequest(), $mockClient);

    expect($responseB->isMocked())
        ->toBeTrue()
        ->and($responseB->status())->toEqual(200)
        ->and($responseB->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);

    // Even though it's a different request, it should use the same fixture

    $responseC = connector()->send(new ErrorRequest(), $mockClient);

    expect($responseC->isMocked())
        ->toBeTrue()
        ->and($responseC->status())->toEqual(200)
        ->and($responseC->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);
});

test('a fixture can be used with a request mock', function () use ($filesystem) {
    $mockClient = new MockClient([
        UserRequest::class => MockResponse::fixture('user'),
    ]);

    expect($filesystem->fileExists('user.json'))->toBeFalse();

    $responseA = connector()->send(new UserRequest(), $mockClient);

    expect($responseA->isMocked())
        ->toBeFalse()
        ->and($responseA->status())->toEqual(200)
        ->and($responseA->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ])
        ->and($filesystem->fileExists('user.json'))->toBeTrue();

    $responseB = connector()->send(new UserRequest(), $mockClient);

    expect($responseB->isMocked())
        ->toBeTrue()
        ->and($responseB->status())->toEqual(200)
        ->and($responseB->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);
});

test('a fixture can be used with a url mock', function () use ($filesystem) {
    $mockClient = new MockClient([
        'tests.saloon.dev/api/user' => MockResponse::fixture('user'), // Test Exact Route
        'tests.saloon.dev/*' => MockResponse::fixture('other'), // Test Wildcard Routes
    ]);

    expect($filesystem->fileExists('user.json'))
        ->toBeFalse()
        ->and($filesystem->fileExists('other.json'))->toBeFalse();

    $responseA = connector()->send(new UserRequest(), $mockClient);

    expect($filesystem->fileExists('user.json'))
        ->toBeTrue()
        ->and($filesystem->fileExists('other.json'))->toBeFalse()
        ->and($responseA->isMocked())->toBeFalse()
        ->and($responseA->status())->toEqual(200)
        ->and($responseA->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);

    $responseB = connector()->send(new ErrorRequest(), $mockClient);

    expect($filesystem->fileExists('user.json'))
        ->toBeTrue()
        ->and($filesystem->fileExists('other.json'))->toBeTrue()
        ->and($responseB->isMocked())->toBeFalse()
        ->and($responseB->status())->toEqual(500)
        ->and($responseB->json())->toEqual([
            'message' => 'Fake Error',
        ]);

    // This should use the first mock

    $responseC = connector()->send(new UserRequest(), $mockClient);

    expect($responseC->isMocked())
        ->toBeTrue()
        ->and($responseC->status())->toEqual(200)
        ->and($responseC->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);

    // Another error request should use the "other" mock

    $responseD = connector()->send(new ErrorRequest(), $mockClient);

    expect($responseD->isMocked())
        ->toBeTrue()
        ->and($responseD->status())->toEqual(500)
        ->and($responseD->json())->toEqual([
            'message' => 'Fake Error',
        ]);
});

test('a fixture can be used with a wildcard url mock', function () {
    $mockClient = new MockClient([
        '*' => MockResponse::fixture('user'), // Test Exact Route
    ]);

    $responseA = connector()->send(new UserRequest(), $mockClient);

    expect($responseA->isMocked())
        ->toBeFalse()
        ->and($responseA->status())->toEqual(200)
        ->and($responseA->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);

    $responseB = connector()->send(new ErrorRequest(), $mockClient);

    expect($responseB->isMocked())
        ->toBeTrue()
        ->and($responseB->status())->toEqual(200)
        ->and($responseB->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);
});

test('a fixture can be used within a closure mock', function () use ($filesystem) {
    $mockClient = new MockClient([
        '*' => function (PendingRequest $pendingRequest) {
            if ($pendingRequest->getRequest() instanceof UserRequest) {
                return MockResponse::fixture('user');
            }

            return MockResponse::fixture('other');
        },
    ]);

    expect($filesystem->fileExists('user.json'))
        ->toBeFalse()
        ->and($filesystem->fileExists('other.json'))->toBeFalse();

    $responseA = connector()->send(new UserRequest(), $mockClient);

    expect($responseA->isMocked())
        ->toBeFalse()
        ->and($responseA->status())->toEqual(200)
        ->and($responseA->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);

    $responseB = connector()->send(new UserRequest(), $mockClient);

    expect($responseB->isMocked())
        ->toBeTrue()
        ->and($responseB->status())->toEqual(200)
        ->and($responseB->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);

    // Now we'll test a different route

    $responseC = connector()->send(new ErrorRequest(), $mockClient);

    expect($responseC->isMocked())
        ->toBeFalse()
        ->and($responseC->status())->toEqual(500)
        ->and($responseC->json())->toEqual([
            'message' => 'Fake Error',
        ]);

    // Another error request should use the "other" mock

    $responseD = connector()->send(new ErrorRequest(), $mockClient);

    expect($responseD->isMocked())
        ->toBeTrue()
        ->and($responseD->status())->toEqual(500)
        ->and($responseD->json())->toEqual([
            'message' => 'Fake Error',
        ]);
});

test('when using the AlwaysThrowRequest trait the response recorder will still record the response', function () {
    $mockClient = new MockClient([
        AlwaysThrowRequest::class => MockResponse::fixture('error'),
    ]);

    $exception = null;

    try {
        connector()->send(new AlwaysThrowRequest(), $mockClient);
    } catch (Exception $exception) {
        //
    }

    expect($exception)->toBeInstanceOf(RequestException::class);

    $fixture = MockResponse::fixture('error')->getMockResponse();

    expect($fixture)->toBeInstanceOf(MockResponse::class);
});

test('a fixture can record the file data from a request that returns a file download', function () {
    $mockClient = new MockClient([
        FileDownloadRequest::class => MockResponse::fixture('file'),
    ]);

    $requestA = new FileDownloadRequest();
    $responseA = connector()->send($requestA, $mockClient);

    expect($responseA->body())->toEqual(file_get_contents('tests/Fixtures/Files/test.pdf'));

    $requestB = new FileDownloadRequest();
    $responseB = connector()->send($requestB, $mockClient);

    expect($responseB->body())->toEqual(file_get_contents('tests/Fixtures/Files/test.pdf'));
});

test('you can create a custom fixture class', function () {
    $mockClient = new MockClient([
        new UserFixture(),
        new UserFixture(),
    ]);

    $responseA = connector()->send(new UserRequest(), $mockClient);

    expect($responseA->isMocked())
        ->toBeFalse()
        ->and($responseA->status())->toEqual(200)
        ->and($responseA->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);

    $responseB = connector()->send(new UserRequest(), $mockClient);

    expect($responseB->isMocked())
        ->toBeTrue()
        ->and($responseB->status())->toEqual(200)
        ->and($responseB->json())->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ]);
});

test('it will throw an exception if the custom fixture class is missing a name', function () {
    $mockClient = new MockClient([
        new MissingNameFixture(),
    ]);

    $this->expectException(FixtureException::class);
    $this->expectExceptionMessage('The fixture must have a name');

    connector()->send(new UserRequest(), $mockClient);
});

test('you can hide sensitive json body parameters and headers before the fixture is stored', function () {
    $mockClient = new MockClient([
        new SafeUserFixture(),
        new SafeUserFixture(),
    ]);

    $responseA = connector()->send(new UserRequest(), $mockClient);
    $responseB = connector()->send(new UserRequest(), $mockClient);

    expect($responseA->json())
        ->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ])
        ->and($responseA->header('Server'))->toEqual('cloudflare')
        ->and($responseA->header('Cache-Control'))->toEqual('no-cache, private')
        ->and($responseA->isFaked())->toBeFalse()
        ->and($responseB->isFaked())->toBeTrue()
        ->and($responseB->json())->toEqual([
            'name' => 'Sxxx',
            'actual_name' => 'REDACTED',
            'twitter' => '@saloonphp',
        ])
        ->and($responseB->header('Server'))->toEqual('secret')
        ->and($responseB->header('Cache-Control'))->toEqual('no-cache, private, yeehaw');

    $fixtureData = json_decode(
        file_get_contents(
            'tests/Fixtures/Saloon/Testing/user.json'
        ),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    expect($fixtureData['headers']['Server'])
        ->toEqual('secret')
        ->and($fixtureData['headers']['Cache-Control'])->toEqual('no-cache, private, yeehaw')
        ->and($fixtureData['data'])->toEqual(
            json_encode([
                'name' => 'Sxxx',
                'actual_name' => 'REDACTED',
                'twitter' => '@saloonphp',
            ], JSON_THROW_ON_ERROR)
        );
});

test('the fixture swap tool works on multiple attempts and recursively', function () {
    $mockClient = new MockClient([
        new SuperheroFixture(),
        new SuperheroFixture(),
    ]);

    $responseA = connector()->send(new PagedSuperheroRequest(), $mockClient);
    $responseB = connector()->send(new PagedSuperheroRequest(), $mockClient);

    expect($responseA->json()['data'])->each
        ->toHaveKey('publisher', 'DC Comics')
        ->and($responseB->json()['data'])->each->toHaveKey('publisher', 'REDACTED');
});

test('you can define a custom redaction method for non-json body fixtures', function () {
    $mockClient = new MockClient([
        new BeforeSaveUserFixture(),
        new BeforeSaveUserFixture(),
    ]);

    $responseA = connector()->send(new UserRequest(), $mockClient);
    $responseB = connector()->send(new UserRequest(), $mockClient);

    expect($responseA->status())
        ->toEqual(200)
        ->and($responseB->status())->toEqual(222);
});

test('you can define regex patterns that should be used to replace the body in fixtures', function () {
    $mockClient = new MockClient([
        new RegexUserFixture(),
        new RegexUserFixture(),
    ]);

    $responseA = connector()->send(new UserRequest(), $mockClient);
    $responseB = connector()->send(new UserRequest(), $mockClient);

    expect($responseA->json())
        ->toEqual([
            'name' => 'Sammyjo20',
            'actual_name' => 'Sam',
            'twitter' => '@carre_sam',
        ])
        ->and($responseA->isFaked())->toBeFalse()
        ->and($responseB->isFaked())->toBeTrue()
        ->and($responseB->json())->toEqual([
            'name' => 'Sxxxmyjo20',
            'actual_name' => 'Sxxx',
            'twitter' => '**REDACTED-TWITTER**',
        ]);

    $fixtureData = json_decode(
        file_get_contents(
            'tests/Fixtures/Saloon/Testing/user.json'
        ),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    expect($fixtureData['data'])->toEqual(json_encode([
        'name' => 'Sxxxmyjo20',
        'actual_name' => 'Sxxx',
        'twitter' => '**REDACTED-TWITTER**',
    ], JSON_THROW_ON_ERROR));
});

test('request and response middleware is invoked when using fake responses', function () {
    $mockClient = new MockClient([
        MockResponse::make(['name' => 'Sam'], 200, ['X-Foo' => 'Bar']),
        MockResponse::make(['name' => 'Alex']),
        MockResponse::make(['error' => 'Server Unavailable'], 500),
    ]);

    $middlewareA = false;
    $middlewareB = false;
    $middlewareC = false;
    $middlewareD = false;

    $connector = new TestConnector();
    $connector->withMockClient($mockClient);

    $request = new UserRequest();

    $connector->middleware()->onRequest(function () use (&$middlewareA) {
        $middlewareA = true;
    });

    $connector->middleware()->onResponse(function () use (&$middlewareB) {
        $middlewareB = true;
    });

    $request->middleware()->onRequest(function () use (&$middlewareC) {
        $middlewareC = true;
    });

    $request->middleware()->onResponse(function () use (&$middlewareD) {
        $middlewareD = true;
    });

    $responseA = $connector->send($request);

    expect($middlewareA)
        ->toBeTrue()
        ->and($middlewareB)->toBeTrue()
        ->and($middlewareC)->toBeTrue()
        ->and($middlewareD)->toBeTrue();
});

test('fixtures are still recorded on the first request', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('user'), // Test Exact Route
    ]);

    connector()->send(new UserRequest(), $mockClient);

    $mockClient->assertSent(UserRequest::class);
});
