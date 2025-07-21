<?php

declare(strict_types=1);

namespace Examples\Connectors;

use Salette\Http\Connector;
use Salette\Traits\Plugins\AcceptsJson;
use Salette\Contracts\Authenticator;
use Salette\Auth\TokenAuthenticator;

class CustomApiConnector extends Connector
{
    use AcceptsJson;

    /**
     * Define the base URL of the API.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://api.custom.com';
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
     * Define the default configuration for requests.
     *
     * @return array<string, mixed>
     */
    protected function defaultConfig(): array
    {
        return [
            'timeout' => 30,
            'http_errors' => false,
        ];
    }

    /**
     * Provide default authentication.
     */
    public function defaultAuth(): ?Authenticator
    {
        return new TokenAuthenticator('your-api-token-here');
    }
}
