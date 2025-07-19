<?php

declare(strict_types=1);

namespace Salette\Repositories;

use Salette\Contracts\Stringable;
use Salette\Traits\Body\CreatesStreamFromString;

class FormBodyRepository extends ArrayBodyRepository implements Stringable
{
    use CreatesStreamFromString;

    /**
     * Convert into a string.
     */
    public function __toString(): string
    {
        return http_build_query($this->all());
    }
}
