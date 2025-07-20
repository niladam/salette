<?php

declare(strict_types=1);

namespace Salette\Traits\OAuth2;

use Salette\Helpers\OAuth2\OAuthConfig;
use Salette\Http\OAuth2\GetClientCredentialsTokenBasicAuthRequest;
use Salette\Requests\Request;

/**
 * @phpstan-ignore trait.unused
 */
trait ClientCredentialsBasicAuthGrant
{
    use ClientCredentialsGrant;

    /**
     * Resolve the access token request
     */
    protected function resolveAccessTokenRequest(
        OAuthConfig $oauthConfig,
        array $scopes = [],
        string $scopeSeparator = ' '
    ): Request {
        return new GetClientCredentialsTokenBasicAuthRequest($oauthConfig, $scopes, $scopeSeparator);
    }
}
