<?php

declare(strict_types=1);

use Salette\Contracts\Body\MergeableBody;
use Salette\Data\MultipartValue;
use Salette\Repositories\MultipartBodyRepository;

test('the store is empty by default', function () {
    $body = new MultipartBodyRepository();

    expect($body->all())->toEqual([]);
});

test('the store can have an array of multipart values provided', function () {
    $body = new MultipartBodyRepository([
        new MultipartValue('name', 'Sam'),
        new MultipartValue('sidekick', 'Mantas'),
    ]);

    expect($body->all())->toEqual([
        new MultipartValue('name', 'Sam'),
        new MultipartValue('sidekick', 'Mantas'),
    ]);
});

test('the store will throw an exception if set value is not an array', function () {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('The value must be an array');

    $body = new MultipartBodyRepository();
    $body->set('123');
});

test('the store will throw an exception if the array does not contain multipart values', function () {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('The value array must only contain Salette\Data\MultipartValue objects');

    new MultipartBodyRepository([
        'name' => 'Sam',
        'sidekick' => new MultipartValue('username', 'Sammyjo20'),
    ]);
});

test('you can set it', function () {
    $body = new MultipartBodyRepository();

    $body->set([
        new MultipartValue('username', 'Sammyjo20'),
    ]);

    expect($body->all())->toEqual([
        new MultipartValue('username', 'Sammyjo20'),
    ]);
});

test('you can add multiple items', function () {
    $body = new MultipartBodyRepository;

    $body->add('name', 'Sam', 'welcome.txt', ['a' => 'b']);

    expect($body->all())->toEqual([
        new MultipartValue('name', 'Sam', 'welcome.txt', ['a' => 'b']),
    ]);

    // Test it gets added to the array

    $body->add('name', 'Charlotte', 'welcome.txt', ['a' => 'b']);

    expect($body->all())->toEqual([
        new MultipartValue('name', 'Sam', 'welcome.txt', ['a' => 'b']),
        new MultipartValue('name', 'Charlotte', 'welcome.txt', ['a' => 'b']),
    ]);
});

test('you can conditionally add items to the array store', function () {
    $body = new MultipartBodyRepository;

    $body->when(true, fn (MultipartBodyRepository $body) => $body->add('name', 'Gareth'));
    $body->when(false, fn (MultipartBodyRepository $body) => $body->add('name', 'Sam'));
    $body->when(true, fn (MultipartBodyRepository $body) => $body->add('sidekick', 'Mantas'));
    $body->when(false, fn (MultipartBodyRepository $body) => $body->add('sidekick', 'Teo'));

    expect($body->all())->toEqual([
        new MultipartValue('name', 'Gareth'),
        new MultipartValue('sidekick', 'Mantas'),
    ]);
});

test('you can delete an item', function () {
    $body = new MultipartBodyRepository();

    $body->add('name', 'Sam');
    $body->remove('name');

    expect($body->all())->toEqual([]);
});

test('you can get an item', function () {
    $body = new MultipartBodyRepository();

    $body->add('name', 'Sam');
    $body->add('friend', 'Chris');

    expect($body->get('name'))
        ->toEqual(new MultipartValue('name', 'Sam'))
        ->and($body->get('friend'))->toEqual(new MultipartValue('friend', 'Chris'));
});

test('you can get multiple items with the same name', function () {
    $body = new MultipartBodyRepository();

    $body->add('name', 'Sam');
    $body->add('name', 'Alex');

    expect($body->get('name'))->toEqual([
        new MultipartValue('name', 'Sam'),
        new MultipartValue('name', 'Alex'),
    ]);
});

test('you can get all items', function () {
    $body = new MultipartBodyRepository();

    $body->add('name', 'Sam');
    $body->add('superhero', 'Iron Man');

    $allResults = [
        new MultipartValue('name', 'Sam'),
        new MultipartValue('superhero', 'Iron Man'),
    ];

    expect($body->all())
        ->toEqual($allResults)
        ->and($body->all())->toEqual($allResults);
});

test('you can merge items together into the body repository', function () {
    $body = new MultipartBodyRepository();

    expect($body)->toBeInstanceOf(MergeableBody::class);

    $body->add('name', 'Sam');
    $body->add('sidekick', 'Mantas');

    $body->merge([new MultipartValue('sidekick', 'Gareth')], [new MultipartValue('superhero', 'Black Widow')]);

    expect($body->all())->toEqual([
        new MultipartValue('name', 'Sam'),
        new MultipartValue('sidekick', 'Mantas'),
        new MultipartValue('sidekick', 'Gareth'),
        new MultipartValue('superhero', 'Black Widow'),
    ]);
});

test('it will throw an exception if the merged items are not MultipartValue objects', function () {
    $body = new MultipartBodyRepository();

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('The value array must only contain Salette\Data\MultipartValue objects');

    $body->merge([new MultipartValue('sidekick', 'Gareth')], ['superhero' => 'Black Widow']);
});

test('you can check if the store is empty or not', function () {
    $body = new MultipartBodyRepository();

    expect($body->isEmpty())
        ->toBeTrue()
        ->and($body->isNotEmpty())->toBeFalse();

    $body->add('name', 'Sam');

    expect($body->isEmpty())
        ->toBeFalse()
        ->and($body->isNotEmpty())->toBeTrue();
});
