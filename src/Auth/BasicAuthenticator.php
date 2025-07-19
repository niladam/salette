<?php

declare(strict_types=1);

namespace Salette\Auth;

use Salette\Contracts\Authenticator;
use Salette\Requests\PendingRequest;

class BasicAuthenticator implements Authenticator
{
    public string $username;

    public string $password;

    public function __construct(
        string $username,
        string $password
    ) {
        $this->password = $password;
        $this->username = $username;
    }

    /**
     * Apply the authentication to the request.
     */
    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest
            ->headers()
            ->add('Authorization', 'Basic ' . base64_encode($this->username . ':' . $this->password));
    }
}
