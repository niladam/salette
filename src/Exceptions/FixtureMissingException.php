<?php

declare(strict_types=1);

namespace Salette\Exceptions;

class FixtureMissingException extends SaletteException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('The fixture "%s" could not be found in storage.', $name));
    }
}
