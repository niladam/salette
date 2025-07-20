<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Mocking;

use Salette\Http\Faking\Fixture;

class SuperheroFixture extends Fixture
{
    protected function defineName(): string
    {
        return 'superhero';
    }

    protected function defineSensitiveJsonParameters(): array
    {
        return [
            'publisher' => 'REDACTED',
        ];
    }
}
