<?php

declare(strict_types=1);

namespace Salette\Auth;

use GuzzleHttp\RequestOptions;
use Salette\Contracts\Authenticator;
use Salette\Exceptions\SaletteException;
use Salette\Requests\PendingRequest;
use Salette\Senders\GuzzleSender;

class DigestAuthenticator implements Authenticator
{
    public string $username;

    public string $password;

    public string $digest;

    public function __construct(string $username, string $password, string $digest)
    {
        $this->username = $username;
        $this->password = $password;
        $this->digest = $digest;
    }

    /**
     * Apply the authentication to the request.
     *
     * @throws SaletteException
     */
    public function set(PendingRequest $pendingRequest): void
    {
        if (! $pendingRequest->getConnector()->sender() instanceof GuzzleSender) {
            throw new SaletteException('The DigestAuthenticator is only supported when using the GuzzleSender.');
        }

        // Note: This authenticator is currently using Guzzle to power the
        // authentication. This will be replaced later in Salette v3 with
        // a first-party implementation of digest authentication.
        //
        // @TODO - investigate first-party implementation details from Saloon

        // See: https://docs.guzzlephp.org/en/stable/request-options.html#auth

        $pendingRequest->config()->add(RequestOptions::AUTH, [$this->username, $this->password, $this->digest]);
    }
}
