<?php

declare(strict_types=1);

namespace Salette\Exceptions;

use Salette\Requests\PendingRequest;

class NoMockResponseFoundException extends SaletteException
{
    public function __construct(PendingRequest $pendingRequest)
    {
        parent::__construct(sprintf('Salette was unable to guess a mock response for your request [%s], 
        consider using a wildcard url mock or a connector mock.', $pendingRequest->getUri()));
    }
}
