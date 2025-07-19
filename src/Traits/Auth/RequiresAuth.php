<?php

declare(strict_types=1);

namespace Salette\Traits\Auth;

use Salette\Contracts\Authenticator;
use Salette\Exceptions\MissingAuthenticatorException;
use Salette\Requests\PendingRequest;

/**
 * @phpstan-ignore trait.unused
 */
trait RequiresAuth
{
    /**
     * Throw an exception if an authenticator is not on the request while it is booting.
     *
     * @throws MissingAuthenticatorException
     */
    public function bootRequiresAuth(PendingRequest $pendingSaletteRequest): void
    {
        $authenticator = $pendingSaletteRequest->getAuthenticator();

        if (! $authenticator instanceof Authenticator) {
            throw new MissingAuthenticatorException($this->getRequiresAuthMessage($pendingSaletteRequest));
        }
    }

    /**
     * Default message.
     */
    protected function getRequiresAuthMessage(PendingRequest $pendingRequest): string
    {
        return sprintf(
            'The "%s" request requires authentication.',
            get_class($pendingRequest->getRequest())
        );
    }
}
