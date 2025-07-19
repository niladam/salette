<?php

declare(strict_types=1);

namespace Salette\Exceptions;

class UnableToCreateFileException extends SaletteException
{
    public function __construct(string $path)
    {
        parent::__construct(sprintf('We were unable to create the "%s" file.', $path));
    }
}
