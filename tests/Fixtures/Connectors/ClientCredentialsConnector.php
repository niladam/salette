<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Connector;
use Salette\Helpers\OAuth2\OAuthConfig;
use Salette\Traits\OAuth2\ClientCredentialsGrant;

class ClientCredentialsConnector extends Connector
{
    use ClientCredentialsGrant;

    /**
     * Define the base URL.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://oauth.saloon.dev';
    }

    /**
     * Define default Oauth config.
     */
    protected function defaultOauthConfig()
    {
        return OAuthConfig::make()
            ->setClientId('client-id')
            ->setClientSecret('client-secret');
    }
}
