<?php

declare(strict_types=1);

namespace Salette\Exceptions;

use Salette\Requests\PendingRequest;
use Throwable;

class FatalRequestException extends SaletteException
{
    /**
     * The PendingRequest
     */
    protected PendingRequest $pendingSaletteRequest;

    public function __construct(Throwable $originalException, PendingRequest $pendingRequest)
    {
        parent::__construct($originalException->getMessage(), $originalException->getCode(), $originalException);

        $this->pendingSaletteRequest = $pendingRequest;
    }

    /**
     * Get the PendingRequest that caused the exception.
     */
    public function getPendingRequest(): PendingRequest
    {
        return $this->pendingSaletteRequest;
    }
}
