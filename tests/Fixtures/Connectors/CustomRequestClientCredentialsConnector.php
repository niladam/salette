<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Helpers\OAuth2\OAuthConfig;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Requests\OAuth\CustomClientCredentialsAccessTokenRequest;

class CustomRequestClientCredentialsConnector extends ClientCredentialsConnector

    /**
     * Resolve the access token request
     *
     * @param  OAuthConfig  $oauthConfig
     * @param  array  $scopes
     * @param  string  $scopeSeparator
     * @return Request
     */
{
    protected function resolveAccessTokenRequest(
        OAuthConfig $oauthConfig,
        array $scopes = [],
        string $scopeSeparator = ' '
    ): CustomClientCredentialsAccessTokenRequest {
        return new CustomClientCredentialsAccessTokenRequest($oauthConfig, $scopes, $scopeSeparator);
    }
}
