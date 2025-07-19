<?php

declare(strict_types=1);

namespace Salette\Http\OAuth2;

use Salette\Contracts\Body\HasBody;
use Salette\Enums\Method;
use Salette\Helpers\OAuth2\OAuthConfig;
use Salette\Requests\Request;
use Salette\Traits\Body\HasFormBody;
use Salette\Traits\Plugins\AcceptsJson;

class GetRefreshTokenRequest extends Request implements HasBody
{
    use AcceptsJson;
    use HasFormBody;

    /**
     * Define the method that the request will use.
     */
    public const METHOD = Method::POST;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return $this->oauthConfig->getTokenEndpoint();
    }

    protected OAuthConfig $oauthConfig;

    protected string $refreshToken;

    public function __construct(OAuthConfig $oauthConfig, string $refreshToken)
    {
        $this->oauthConfig = $oauthConfig;
        $this->refreshToken = $refreshToken;
    }

    /**
     * Register the default data.
     *
     * @return array{
     *     grant_type: string,
     *     refresh_token: string,
     *     client_id: string,
     *     client_secret: string,
     * }
     */
    public function defaultBody(): array
    {
        return [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refreshToken,
            'client_id' => $this->oauthConfig->getClientId(),
            'client_secret' => $this->oauthConfig->getClientSecret(),
        ];
    }
}
