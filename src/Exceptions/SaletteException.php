<?php

declare(strict_types=1);

namespace Salette\Exceptions;

use Exception;

class SaletteException extends Exception
{
    public static function withMessage(string $message): self
    {
        return new self($message);
    }
}
