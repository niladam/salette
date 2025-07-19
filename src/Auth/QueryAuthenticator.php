<?php

declare(strict_types=1);

namespace Salette\Auth;

use Salette\Contracts\Authenticator;
use Salette\Requests\PendingRequest;

class QueryAuthenticator implements Authenticator
{
    public string $parameter;

    public string $value;

    public function __construct(string $parameter, string $value)
    {
        $this->parameter = $parameter;
        $this->value = $value;
    }

    /**
     * Apply the authentication to the request.
     */
    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest->query()->add($this->parameter, $this->value);
    }
}
