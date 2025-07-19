<?php

declare(strict_types=1);

namespace Salette\Exceptions;

class UnableToCreateDirectoryException extends SaletteException
{
    public function __construct(string $directory)
    {
        parent::__construct(sprintf('Unable to create the directory: %s.', $directory));
    }
}
