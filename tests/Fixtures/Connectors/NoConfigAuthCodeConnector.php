<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Connector;
use Salette\Traits\OAuth2\AuthorizationCodeGrant;

class NoConfigAuthCodeConnector extends Connector
{
    use AuthorizationCodeGrant;

    /**
     * Define the base URL.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://oauth.saloon.dev';
    }
}
