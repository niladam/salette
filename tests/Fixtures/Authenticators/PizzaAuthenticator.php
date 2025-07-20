<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Authenticators;

use Salette\Contracts\Authenticator;
use Salette\Requests\PendingRequest;

class PizzaAuthenticator implements Authenticator
{
    public string $pizza;

    public string $drink;

    public function __construct(
        string $pizza,
        string $drink
    ) {
        $this->drink = $drink;
        $this->pizza = $pizza;
        //
    }

    /**
     * Set the pending request.
     */
    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('X-Pizza', $this->pizza);
        $pendingRequest->headers()->add('X-Drink', $this->drink);

        $pendingRequest->config()->add('debug', true);
    }
}
