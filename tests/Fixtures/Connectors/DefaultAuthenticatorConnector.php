<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Connector;
use Salette\Contracts\Authenticator;
use Salette\Traits\Plugins\AcceptsJson;
use Salette\Auth\TokenAuthenticator;

class DefaultAuthenticatorConnector extends Connector
{
    use AcceptsJson;

    /**
     * Define the base url of the api.
     */
    public function resolveBaseUrl(): string
    {
        return apiUrl();
    }

    /**
     * Define the base headers that will be applied in every request.
     *
     * @return string[]
     */
    public function defaultHeaders(): array
    {
        return [];
    }

    /**
     * Provide default authentication.
     */
    public function defaultAuth(): ?Authenticator
    {
        return new TokenAuthenticator('yee-haw-connector');
    }
}
