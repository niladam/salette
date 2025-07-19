<?php

declare(strict_types=1);

namespace Salette\Contracts;

use Salette\Requests\PendingRequest;

interface Authenticator
{
    /**
     * Apply the authentication to the request.
     */
    public function set(PendingRequest $pendingRequest): void;
}
