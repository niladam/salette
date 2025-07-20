<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Collection;
use Psr\Http\Message\RequestInterface;
use Salette\Contracts\ArrayStore;
use Salette\Exceptions\RequestException;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Http\Response as SaloonResponse;
use Salette\Requests\PendingRequest;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Requests\CustomEndpointRequest;
use Salette\Tests\Fixtures\Requests\UserRequest;
use Symfony\Component\DomCrawler\Crawler;

test('you can get the original pending request', function () {
    $mockClient = new MockClient([
        MockResponse::make(['foo' => 'bar'], 200, ['X-Custom-Header' => 'Howdy']),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    $pendingRequest = $response->getPendingRequest();

    expect($pendingRequest)
        ->toBeInstanceOf(PendingRequest::class)
        ->and($pendingRequest->getRequest())
        ->toBeInstanceOf(UserRequest::class);
});

test('you can get the connector', function () {
    $mockClient = new MockClient([
        MockResponse::make(['foo' => 'bar'], 200, ['X-Custom-Header' => 'Howdy']),
    ]);

    $request = new UserRequest();
    $connector = new TestConnector();
    $response = $connector->send($request, $mockClient);

    expect($response->getConnector())->toBe($connector);
});

test('you can get the original request', function () {
    $mockClient = new MockClient([
        MockResponse::make(['foo' => 'bar'], 200, ['X-Custom-Header' => 'Howdy']),
    ]);

    $request = new UserRequest();
    $response = connector()->send($request, $mockClient);

    expect($response->getRequest())->toBe($request);
});

test('you can get the psr-7 request', function () {
    $mockClient = new MockClient([
        MockResponse::make(['foo' => 'bar'], 200, ['X-Custom-Header' => 'Howdy']),
    ]);

    $request = new UserRequest();
    $response = connector()->send($request, $mockClient);

    expect($response->getPsrRequest())->toBeInstanceOf(RequestInterface::class);
});

test('it will throw an exception when you use the throw method', function () {
    $mockClient = new MockClient([
        MockResponse::make([], 500),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    $this->expectException(RequestException::class);

    $response->throw();
});

test('it wont throw an exception if the request did not fail', function () {
    $mockClient = new MockClient([
        MockResponse::make([], 200),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    expect($response)->throw()->toBe($response);
});

test('to exception will return a saloon request exception', function () {
    $mockClient = new MockClient([
        MockResponse::make([], 500),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);
    $exception = $response->toException();

    expect($exception)->toBeInstanceOf(RequestException::class);
});

test('to exception wont return anything if the request did not fail', function () {
    $mockClient = new MockClient([
        MockResponse::make([], 200),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);
    $exception = $response->toException();

    expect($exception)->toBeNull();
});

test('the onError method will run a custom closure', function () {
    $mockClient = new MockClient([
        MockResponse::make([], 500),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);
    $count = 0;

    $response->onError(function () use (&$count) {
        $count++;
    });

    expect($count)->toBe(1);
});

test('the object method will return an object', function () {
    $data = ['name' => 'Sam', 'work' => 'Codepotato'];

    $mockClient = new MockClient([
        MockResponse::make($data, 500),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    $dataAsObject = (object) $data;

    expect($response)->object()->toEqual($dataAsObject);
});

test('the object method with a dot notation key value will return a nested string', function () {
    $data = [
        'contacts' => [
            ['name' => 'Sam', 'work' => 'Codepotato'],
            ['name' => 'Braunson', 'work' => 'Geekybeaver'],
        ],
    ];

    $mockClient = new MockClient([
        MockResponse::make($data, 500),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    expect($response)->object('contacts.1.name')->toEqual('Braunson');
});

test('the collect method will return a collection', function () {
    $mockClient = new MockClient([
        MockResponse::make(['name' => 'Sam', 'work' => 'Codepotato'], 500),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);
    $collection = $response->collect();

    expect($collection)
        ->toBeInstanceOf(Collection::class)
        ->and($collection)->toHaveCount(2)
        ->and($collection['name'])->toEqual('Sam')
        ->and($collection['work'])->toEqual('Codepotato')
        ->and($response->collect('name'))->toArray()->toEqual(['Sam'])
        ->and($response->collect('age'))->toBeEmpty();
});

test('the json method will return empty array if body is empty', function () {
    $mockClient = new MockClient([
        MockResponse::make('', 404),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    expect($response->json())->toBe([]);
});

test('the toPsrResponse method will return a guzzle response', function () {
    $mockClient = new MockClient([
        MockResponse::make(['name' => 'Sam', 'work' => 'Codepotato'], 500),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    expect($response)->getPsrResponse()->toBeInstanceOf(Response::class);
});

test('you can get an individual header from the response', function () {
    $mockClient = new MockClient([
        MockResponse::make(['name' => 'Sam', 'work' => 'Codepotato'], 200, ['X-Greeting' => 'Howdy']),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    expect($response)
        ->header('X-Greeting')->toEqual('Howdy')
        ->and($response)->header('X-Missing')->toBeEmpty();
});

test('it will convert the body to string if the cast is used', function () {
    $data = ['name' => 'Sam', 'work' => 'Codepotato'];

    $mockClient = new MockClient([
        MockResponse::make($data, 200, ['X-Greeting' => 'Howdy']),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    expect((string) $response)->toEqual(json_encode($data));
});

test('it checks statuses correctly', function () {
    $mockClient = new MockClient([
        MockResponse::make(['name' => 'Sam', 'work' => 'Codepotato'], 200, ['X-Greeting' => 'Howdy']),
        MockResponse::make(['name' => 'Sam', 'work' => 'Codepotato'], 500, ['X-Greeting' => 'Howdy']),
        MockResponse::make(['name' => 'Sam', 'work' => 'Codepotato'], 302, ['X-Greeting' => 'Howdy']),
    ]);

    $responseA = connector()->send(new UserRequest(), $mockClient);

    expect($responseA)
        ->successful()->toBeTrue()
        ->and($responseA)->ok()->toBeTrue()
        ->and($responseA)->redirect()->toBeFalse()
        ->and($responseA)->failed()->toBeFalse()
        ->and($responseA)->serverError()->toBeFalse();

    $responseB = connector()->send(new UserRequest(), $mockClient);

    expect($responseB)
        ->successful()->toBeFalse()
        ->and($responseB)->ok()->toBeFalse()
        ->and($responseB)->redirect()->toBeFalse()
        ->and($responseB)->failed()->toBeTrue()
        ->and($responseB)->serverError()->toBeTrue();

    $responseC = connector()->send(new UserRequest(), $mockClient);

    expect($responseC)
        ->successful()->toBeFalse()
        ->and($responseC)->ok()->toBeFalse()
        ->and($responseC)->redirect()->toBeTrue()
        ->and($responseC)->failed()->toBeFalse()
        ->and($responseC)->serverError()->toBeFalse();
});

test('the xml method will return xml as an array', function () {
    $mockClient = new MockClient([
        new MockResponse(
            '<SaveContactResponse xmlns="http://schemas.datacontract.org/2004/07/SmashFly.WebServices.ContactManagerService.v2">
                        <ContactId>1168255</ContactId>
                        <Errors nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"/>
                        <HasErrors>false</HasErrors>
                    </SaveContactResponse>',
            200
        ),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);
    $simpleXml = $response->xml();

    expect($simpleXml)->toBeInstanceOf(SimpleXMLElement::class);
});

test('the xmlReader method will return an XmlReader instance', function () {
    $mockClient = new MockClient([
        MockResponse::fixture('xml'),
    ]);

    $request = new CustomEndpointRequest();
    $request->setEndpoint('/breakfast-menu');

    $response = connector()->send($request, $mockClient);
    $reader = $response->xmlReader();

    expect($reader->value('food.2.name')->sole())
        ->toBe('Berry-Berry Belgian Waffles');
});

test('the headers method returns an array store', function () {
    $mockClient = new MockClient([
        MockResponse::make(['name' => 'Sam', 'work' => 'Codepotato'], 200, ['X-Greeting' => 'Howdy']),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    expect($response->headers())->toBeInstanceOf(ArrayStore::class);
});

test(
    'headers with a single value will have just the string value but headers with multiple values will be an array',
    function () {
        $mockClient = new MockClient([
        MockResponse::make(['name' => 'Sam', 'work' => 'Codepotato'], 200, ['X-Greeting' => 'Howdy', 'X-Farewell' => ['Goodbye', 'Sam']]),
        ]);

        $response = connector()->send(new UserRequest(), $mockClient);

        expect($response->headers()->get('X-Greeting'))
            ->toEqual('Howdy')
            ->and($response->headers()->get('X-Farewell'))->toEqual(['Goodbye', 'Sam'])
            ->and($response->header('X-Greeting'))->toEqual('Howdy')
            ->and($response->header('X-Farewell'))->toEqual(['Goodbye', 'Sam']);
    }
);

test('the dom method will return a crawler instance', function () {
    $dom = '<p>Howdy <i>Partner</i></p>';

    $mockClient = new MockClient([
        new MockResponse($dom),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    expect($response->dom())
        ->toBeInstanceOf(Crawler::class)
        ->and($response->dom())->toEqual(new Crawler($dom));
});

test('when using the body methods the stream is rewound back to the start', function () {
    $mockClient = new MockClient([
        MockResponse::make(['foo' => 'bar'], 200, ['X-Custom-Header' => 'Howdy']),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    expect($response->json())
        ->toEqual(['foo' => 'bar'])
        ->and($response->array())->toEqual(['foo' => 'bar'])
        ->and($response->body())->toEqual('{"foo":"bar"}')
        ->and(stream_get_contents($response->getRawStream()))->toEqual('{"foo":"bar"}')
        ->and($response->object())->toEqual((object)['foo' => 'bar']);
});

test('it can convert the response to a data url', function () {
    $mockClient = new MockClient([
        MockResponse::make(['foo' => 'bar'], 200, ['Content-Type' => 'application/json;encoding=utf-8']),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    expect($response->dataUrl())->toEqual('data:application/json;encoding=utf-8;base64,eyJmb28iOiJiYXIifQ==');
});

test('if a response is changed through middleware the new instance is used', function () {
    $mockClient = new MockClient([
        MockResponse::make(['foo' => 'bar'], 200, ['X-Custom-Header' => 'Howdy']),
    ]);

    $connector = new TestConnector();

    $connector->middleware()->onResponse(function (SaloonResponse $response) {
        // Let's modify the body while sending!
        $psrResponse = $response->getPsrResponse();
        $newPsrResponse = $psrResponse->withBody(Utils::streamFor('Hello World!'));

        return $response::fromPsrResponse($newPsrResponse, $response->getPendingRequest(), $response->getPsrRequest());
    });

    $response = $connector->send(new UserRequest(), $mockClient);

    expect($response->body())
        ->toEqual('Hello World!')
        ->and($response->headers()->all())->toEqual(['X-Custom-Header' => 'Howdy']);
});

test('you can get the response stream as a raw resource', function () {
    $response = connector()->send(new UserRequest());

    $resource = $response->getRawStream();

    expect($resource)
        ->toBeResource()
        ->and(stream_get_contents($resource))->toEqual(
            '{"name":"Sammyjo20","actual_name":"Sam","twitter":"@carre_sam"}'
        );
});

test('you can get the response stream as a raw resource with a mock response', function () {
    $mockClient = new MockClient([
        MockResponse::make(['foo' => 'bar'], 200, ['X-Custom-Header' => 'Howdy']),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    $resource = $response->getRawStream();

    expect($resource)
        ->toBeResource()
        ->and(stream_get_contents($resource))->toEqual('{"foo":"bar"}');
});

test('you can get save the response to a file', function ($resourceOrPath) {
    $mockClient = new MockClient([
        MockResponse::make(['foo' => 'bar'], 200, ['X-Custom-Header' => 'Howdy']),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);
    $response->saveBodyToFile($resourceOrPath);

    if (is_string($resourceOrPath)) {
        $path = 'tests/Fixtures/Saloon/Testing/streamToFile1.json';
    } else {
        $path = 'tests/Fixtures/Saloon/Testing/streamToFile2.json';
    }

    expect(file_get_contents($path))->toEqual('{"foo":"bar"}');
})->with([
    'tests/Fixtures/Saloon/Testing/streamToFile1.json',
    fn () => fopen('tests/Fixtures/Saloon/Testing/streamToFile2.json', 'wb+'),
]);

test('the response is macroable', function () {
    SaloonResponse::macro('yee', fn () => 'haw');

    $mockClient = new MockClient([
        MockResponse::make(['foo' => 'bar'], 200, ['X-Custom-Header' => 'Howdy']),
    ]);

    $response = connector()->send(new UserRequest(), $mockClient);

    expect($response->yee())->toEqual('haw');
});

test('can determine if response is JSON', function () {
    $mockClient = new MockClient([
        // JSON content type
        MockResponse::make(['foo' => 'bar'], 200, ['Content-Type' => 'application/json']),
        // JSON with charset
        MockResponse::make(['foo' => 'bar'], 200, ['Content-Type' => 'application/json; charset=utf-8']),
        // Non-JSON content type
        MockResponse::make('plain text', 200, ['Content-Type' => 'text/plain']),
        // No content type
        MockResponse::make('no content type', 200, []),
    ]);

    $connector = connector();

    $response = $connector->send(new UserRequest(), $mockClient);
    expect($response->isJson())->toBeTrue();

    $response = $connector->send(new UserRequest(), $mockClient);
    expect($response->isJson())->toBeTrue();

    $response = $connector->send(new UserRequest(), $mockClient);
    expect($response->isJson())->toBeFalse();

    $response = $connector->send(new UserRequest(), $mockClient);
    expect($response->isJson())->toBeFalse();
});

test('can determine if response is XML', function () {
    $mockClient = new MockClient([
        // XML content type
        MockResponse::make('<?xml version="1.0"?><root></root>', 200, ['Content-Type' => 'application/xml']),
        // XML with charset
        MockResponse::make('<?xml version="1.0"?><root></root>', 200, ['Content-Type' => 'text/xml; charset=utf-8']),
        // Non-XML content type
        MockResponse::make('plain text', 200, ['Content-Type' => 'text/plain']),
        // No content type
        MockResponse::make('no content type', 200, []),
    ]);

    $connector = connector();

    $response = $connector->send(new UserRequest(), $mockClient);
    expect($response->isXml())->toBeTrue();

    $response = $connector->send(new UserRequest(), $mockClient);
    expect($response->isXml())->toBeTrue();

    $response = $connector->send(new UserRequest(), $mockClient);
    expect($response->isXml())->toBeFalse();

    $response = $connector->send(new UserRequest(), $mockClient);
    expect($response->isXml())->toBeFalse();
});
