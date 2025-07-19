<?php

declare(strict_types=1);

namespace Salette\Exceptions;

class DuplicatePipeNameException extends SaletteException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('The "%s" pipe already exists on the pipeline', $name));
    }
}
