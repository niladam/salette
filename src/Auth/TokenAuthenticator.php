<?php

declare(strict_types=1);

namespace Salette\Auth;

use Salette\Contracts\Authenticator;
use Salette\Requests\PendingRequest;

class TokenAuthenticator implements Authenticator
{
    public string $token;

    public string $prefix = 'Bearer';

    public function __construct(
        string $token,
        string $prefix = 'Bearer'
    ) {
        $this->prefix = $prefix;
        $this->token = $token;
    }

    /**
     * Apply the authentication to the request.
     */
    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('Authorization', trim($this->prefix . ' ' . $this->token));
    }
}
