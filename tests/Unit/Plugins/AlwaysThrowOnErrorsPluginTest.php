<?php

declare(strict_types=1);

use Salette\Exceptions\RequestException;
use Salette\Tests\Fixtures\Requests\AlwaysThrowRequest;

test('it always throws an error if the plugin has been added', function () {
    $this->expectException(RequestException::class);

    connector()->send(new AlwaysThrowRequest());
});
