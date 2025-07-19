<?php

declare(strict_types=1);

namespace Salette\Exceptions;

class DirectoryNotFoundException extends SaletteException
{
    public function __construct(string $directory)
    {
        parent::__construct(sprintf('The directory "%s" does not exist or is not a valid directory.', $directory));
    }
}
