<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Connector;
use Salette\Traits\OAuth2\ClientCredentialsGrant;

class NoConfigClientCredentialsConnector extends Connector
{
    use ClientCredentialsGrant;

    /**
     * Define the base URL.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://oauth.saloon.dev';
    }
}
