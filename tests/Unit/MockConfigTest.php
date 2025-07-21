<?php

declare(strict_types=1);

use Salette\Exceptions\FixtureMissingException;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\MockConfig;
use Salette\Tests\Fixtures\Requests\UserRequest;

afterEach(function () {
    MockConfig::setFixturePath('tests/Fixtures/Saloon');
});

test('you can change the default fixture path', function () {
    expect(MockConfig::getFixturePath())->toEqual('tests/Fixtures/Salette');

    MockConfig::setFixturePath('saloon-requests/responses');

    expect(MockConfig::getFixturePath())->toEqual('saloon-requests/responses');
});

test('you can throw an exception if the fixture does not exist', function () {
    MockConfig::setFixturePath('tests/Fixtures/Saloon');

    expect(MockConfig::isThrowingOnMissingFixtures())->toBeFalse();

    MockConfig::throwOnMissingFixtures();

    $mockClient = new MockClient([
        MockResponse::fixture('example'),
    ]);

    connector()->send(new UserRequest(), $mockClient);
})->throws(FixtureMissingException::class, 'The fixture "example.json" could not be found in storage.');
