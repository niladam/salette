<?php

declare(strict_types=1);

namespace Salette\Exceptions;

use Salette\Http\Response;

class InvalidResponseClassException extends SaletteException
{
    public function __construct(?string $message = null)
    {
        parent::__construct(
            $message ?? sprintf(
                'The provided response must exist and implement the %s contract.',
                Response::class
            )
        );
    }
}
