<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Mocking;

use Salette\Http\Faking\Fixture;

class UserFixture extends Fixture
{
    /**
     * Define the name of the fixture
     */
    protected function defineName(): string
    {
        return 'user';
    }
}
