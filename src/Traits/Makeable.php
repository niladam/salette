<?php

declare(strict_types=1);

namespace Salette\Traits;

trait Makeable
{
    /**
     * Instantiate a new class with the arguments.
     */
    public static function make(...$arguments): self
    {
        return new static(...$arguments);
    }
}
