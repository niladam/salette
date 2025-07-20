<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Utils;
use Salette\Repositories\StreamBodyRepository;

test('the store is empty by default', function () {
    $body = new StreamBodyRepository();

    expect($body->all())->toBeNull();
    expect($body->get())->toBeNull();
});


test('the store can have a default stream provided', function () {
    $resource = tmpfile();

    $body = new StreamBodyRepository($resource);

    expect($body->all())
        ->toEqual($resource)
        ->and($body->get())->toEqual($resource);
});

test('you can set it', function () {
    $resourceA = fopen('php://memory', 'rw+');
    fwrite($resourceA, 'Howdy');

    $resourceB = fopen('php://memory', 'rw+');
    fwrite($resourceB, 'Yeehaw');

    $body = new StreamBodyRepository($resourceA);

    $body->set($resourceB);

    expect($body->get())->toEqual($resourceB);
});

test('you can set an instance of StreamInterface', function () {
    $streamA = Utils::streamFor('Howdy!');
    $streamB = Utils::streamFor('Partner!');

    $body = new StreamBodyRepository($streamA);
    $body->set($streamB);

    expect($body->get())->toBe($streamB);
});

test('you can conditionally set on the store', function () {
    $body = new StreamBodyRepository();

    $resourceA = fopen('php://memory', 'rw+');
    fwrite($resourceA, 'Howdy');

    $resourceB = fopen('php://memory', 'rw+');
    fwrite($resourceB, 'Yeehaw');

    $body->when(true, fn (StreamBodyRepository $body) => $body->set($resourceA));
    $body->when(false, fn (StreamBodyRepository $body) => $body->set($resourceB));

    expect($body->get())->toEqual($resourceA);
});

test('you can check if the store is empty or not', function () {
    $body = new StreamBodyRepository();

    expect($body->isEmpty())
        ->toBeTrue()
        ->and($body->isNotEmpty())->toBeFalse();

    $body->set(tmpfile());

    expect($body->isEmpty())
        ->toBeFalse()
        ->and($body->isNotEmpty())->toBeTrue();
});

test('it will throw an exception if the value is not a resource or StreamInterface when instantiating', function ($value) {
    $this->expectException(InvalidArgumentException::class);

    new StreamBodyRepository($value);
})->with([
    fn () => 'Howdy',
    fn () => 123,
    fn () => [],
    fn () => false,
]);

test('it will throw an exception if the value is not a resource or StreamInterface when setting', function ($value) {
    $this->expectException(InvalidArgumentException::class);

    new StreamBodyRepository($value);
})->with([
    fn () => 'Howdy',
    fn () => 123,
    fn () => [],
    fn () => false,
]);

test('it allows null values', function () {
    $body = new StreamBodyRepository(null);

    expect($body->get())
        ->toBeNull()
        ->and($body->isEmpty())->toBeTrue();

    $body->set(null);

    expect($body->get())
        ->toBeNull()
        ->and($body->isEmpty())->toBeTrue();
});
