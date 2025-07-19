<?php

declare(strict_types=1);

namespace Salette\Exceptions;

class StrayRequestException extends SaletteException
{
    public function __construct()
    {
        parent::__construct('Attempted to make a real API request! Make sure to use a mock response or fixture.');
    }
}
