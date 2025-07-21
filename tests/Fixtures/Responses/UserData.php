<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Responses;

class UserData
{
    public ?string $foo;
    /**
     * CustomResponse constructor.
     */
    public function __construct(
        ?string $foo
    ) {
        $this->foo = $foo;
    }
}
