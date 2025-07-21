<?php

declare(strict_types=1);

namespace App\Connectors;

use Salette\Http\Connector;
use Salette\Traits\Plugins\AcceptsJson;
use Salette\Contracts\Authenticator;
use Salette\Auth\BasicAuthenticator;

class JiraApiConnector extends Connector
{
    use AcceptsJson;

    /**
     * Define the base URL of the API.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://your-domain.atlassian.net/rest/api/3';
    }

    /**
     * Define the base headers that will be applied in every request.
     *
     * @return string[]
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Provide default authentication.
     */
    public function defaultAuth(): ?Authenticator
    {
        return new BasicAuthenticator('username', 'password');
    }
}
