<?php

declare(strict_types=1);

namespace Salette\Auth;

use Salette\Contracts\Authenticator;
use Salette\Requests\PendingRequest;

class HeaderAuthenticator implements Authenticator
{
    public string $accessToken;

    public string $headerName;

    public function __construct(string $accessToken, string $headerName = 'Authorization')
    {
        $this->accessToken = $accessToken;
        $this->headerName = $headerName;
    }

    /**
     * Apply the authentication to the request.
     */
    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add($this->headerName, $this->accessToken);
    }
}
