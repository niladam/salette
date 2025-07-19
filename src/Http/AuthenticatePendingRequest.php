<?php

declare(strict_types=1);

namespace Salette\Http;

use Salette\Contracts\Authenticator;
use Salette\Requests\PendingRequest;

class AuthenticatePendingRequest
{
    /**
     * Authenticate the pending request
     */
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $authenticator = $pendingRequest->getAuthenticator();

        if ($authenticator instanceof Authenticator) {
            $authenticator->set($pendingRequest);
        }

        return $pendingRequest;
    }
}
