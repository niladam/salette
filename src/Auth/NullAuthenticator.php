<?php

declare(strict_types=1);

namespace Salette\Auth;

use Salette\Contracts\Authenticator;
use Salette\Requests\PendingRequest;

class NullAuthenticator implements Authenticator
{
    /**
     * Apply the authentication to the request.
     */
    public function set(PendingRequest $pendingRequest): void
    {
        //
    }
}
