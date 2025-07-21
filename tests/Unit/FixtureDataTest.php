<?php

declare(strict_types=1);

use Pest\Expectation;
use Salette\Data\RecordedResponse;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Tests\Fixtures\Requests\DTORequest;

test('you can create a fixture data object from a file string', function () {
    $data = [
        'statusCode' => 200,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'data' => [
            'name' => 'Sam',
        ],
    ];

    $fixtureData = RecordedResponse::fromFile(json_encode($data));

    expect($fixtureData->statusCode)
        ->toEqual($data['statusCode'])
        ->and($fixtureData->headers)->toEqual($data['headers'])
        ->and($fixtureData->data)->toEqual($data['data']);
});

test('you can create a mock response from fixture data', function () {
    $data = [
        'statusCode' => 200,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'data' => [
            'name' => 'Sam',
        ],
    ];

    $fixtureData = RecordedResponse::fromFile(json_encode($data));
    $mockResponse = $fixtureData->toMockResponse();

    expect($mockResponse)->toEqual(new MockResponse($data['data'], $data['statusCode'], $data['headers']));
});

test('you can json serialize the fixture data or convert it into a file', function (array $data, $expected = null) {
    $expected ??= $data;

    $fixtureData = RecordedResponse::fromFile(json_encode($data, JSON_PRETTY_PRINT));

    $serialized = json_encode($fixtureData, JSON_PRETTY_PRINT);

    expect($serialized)
        ->toEqual(json_encode($expected, JSON_PRETTY_PRINT))
        ->and($fixtureData->toFile())->toEqual($serialized);
})->with([
    'without context key' => [
        [
            'statusCode' => 200,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'data' => [
                'name' => 'Sam',
            ],
        ],
        [
            'statusCode' => 200,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'data' => [
                'name' => 'Sam',
            ],
            'context' => [],
        ],
    ],
    'with context key' => [
        [
            'statusCode' => 200,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'data' => [
                'name' => 'Sam',
            ],
            'context' => [],
        ],
    ],
    'with context data' => [
        [
            'statusCode' => 200,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'data' => [
                'name' => 'Sam',
            ],
            'context' => [
                'test' => 'you can json serialize the fixture data or convert it into a file',
            ],
        ],
    ],
]);

test('arbitrary data can be merged in the fixture', function () {
    $response = connector()->send(new DTORequest(), new MockClient([
        MockResponse::fixture('user')->merge([
            'name' => 'Sam Carré',
        ]),
    ]));

    expect($response->dto())
        ->name->toBe('Sam Carré')
        ->actualName->toBe('Sam')
        ->twitter->toBe('@carre_sam');
});

test('arbitrary data using dot-notation can be merged in the fixture', function () {
    // Temporarily set the fixture path to use the Saloon fixtures which have the correct structure
    $originalPath = \Salette\MockConfig::getFixturePath();
    \Salette\MockConfig::setFixturePath('tests/Fixtures/Saloon');
    
    try {
        $response = connector()->send(new DTORequest(), new MockClient([
            MockResponse::fixture('users')->merge([
                'data.0.twitter' => '@jon_doe',
            ]),
        ]));

        $data = $response->json('data');

        expect($data)
            ->toHaveCount(2)
            ->sequence(
                fn (Expectation $e) => $e->twitter->toBe('@jon_doe'),
                fn (Expectation $e) => $e->twitter->toBe('@janedoe'),
            );
    } finally {
        // Restore the original fixture path
        \Salette\MockConfig::setFixturePath($originalPath);
    }
});

test('a closure can be used to modify the mock response data', function () {
    // Temporarily set the fixture path to use the Saloon fixtures which have the correct structure
    $originalPath = \Salette\MockConfig::getFixturePath();
    \Salette\MockConfig::setFixturePath('tests/Fixtures/Saloon');
    
    try {
        $response = connector()->send(new DTORequest(), new MockClient([
            MockResponse::fixture('users')->through(fn (array $data) => array_merge_recursive($data, [
                'data' => [
                    [
                        'name' => 'Sam',
                        'actual_name' => 'Carré',
                        'twitter' => '@carre_sam',
                    ],
                ],
            ])),
        ]));

        expect($response->json('data'))
            ->toHaveCount(3)
            ->sequence(
                fn (Expectation $e) => $e->twitter->toBe('@jondoe'),
                fn (Expectation $e) => $e->twitter->toBe('@janedoe'),
                fn (Expectation $e) => $e->twitter->toBe('@carre_sam'),
            );
    } finally {
        // Restore the original fixture path
        \Salette\MockConfig::setFixturePath($originalPath);
    }
});
