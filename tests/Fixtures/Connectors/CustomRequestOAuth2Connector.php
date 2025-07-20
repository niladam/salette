<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Helpers\OAuth2\OAuthConfig;
use Salette\Http\Connector;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Requests\OAuth\CustomAccessTokenRequest;
use Salette\Tests\Fixtures\Requests\OAuth\CustomOAuthUserRequest;
use Salette\Tests\Fixtures\Requests\OAuth\CustomRefreshTokenRequest;
use Salette\Traits\OAuth2\AuthorizationCodeGrant;

class CustomRequestOAuth2Connector extends Connector
{
    use AuthorizationCodeGrant;

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
            ->setRedirectUri('https://my-app.saloon.dev/auth/callback');
    }

    /**
     * Resolve the access token request
     */
    protected function resolveAccessTokenRequest(string $code, OAuthConfig $oauthConfig): CustomAccessTokenRequest
    {
        return new CustomAccessTokenRequest($code, $oauthConfig);
    }

    /**
     * Resolve the refresh token request
     */
    protected function resolveRefreshTokenRequest(
        OAuthConfig $oauthConfig,
        string $refreshToken
    ): CustomRefreshTokenRequest {
        return new CustomRefreshTokenRequest($oauthConfig, $refreshToken);
    }

    /**
     * Resolve the user request
     */
    protected function resolveUserRequest(OAuthConfig $oauthConfig): CustomOAuthUserRequest
    {
        return new CustomOAuthUserRequest($oauthConfig);
    }
}
