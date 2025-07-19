<?php

declare(strict_types=1);

namespace Salette\Auth;

use Salette\Contracts\Authenticator;
use Salette\Requests\PendingRequest;

class MultiAuthenticator implements Authenticator
{
    /**
     * Authenticators
     *
     * @var array<Authenticator>
     */
    protected array $authenticators;

    public function __construct(Authenticator ...$authenticators)
    {
        $this->authenticators = $authenticators;
    }

    /**
     * Apply the authentication to the request.
     */
    public function set(PendingRequest $pendingRequest): void
    {
        foreach ($this->authenticators as $authenticator) {
            $authenticator->set($pendingRequest);
        }
    }
}
