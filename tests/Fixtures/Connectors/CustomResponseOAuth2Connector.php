<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Helpers\OAuth2\OAuthConfig;
use Salette\Http\Connector;
use Salette\Tests\Fixtures\Authenticators\CustomOAuthAuthenticator;
use Salette\Traits\OAuth2\AuthorizationCodeGrant;

class CustomResponseOAuth2Connector extends Connector
{
    use AuthorizationCodeGrant;

    protected string $greeting;

    public function __construct(string $greeting)
    {
        $this->greeting = $greeting;
        //
    }

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
            ->setClientSecret('client-secret')
            ->setRedirectUri('https://my-app.saloon.dev/oauth/redirect');
    }

    /**
     * Create the OAuth authenticator
     */
    protected function createOAuthAuthenticator(
        string $accessToken,
        $refreshToken = null,
        $expiresAt = null
    ): CustomOAuthAuthenticator {
        return new CustomOAuthAuthenticator($accessToken, $this->greeting, $refreshToken, $expiresAt);
    }
}
