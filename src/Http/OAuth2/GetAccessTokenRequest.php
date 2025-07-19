<?php

declare(strict_types=1);

namespace Salette\Http\OAuth2;

use Salette\Contracts\Body\HasBody;
use Salette\Enums\Method;
use Salette\Helpers\OAuth2\OAuthConfig;
use Salette\Requests\Request;
use Salette\Traits\Body\HasFormBody;
use Salette\Traits\Plugins\AcceptsJson;

class GetAccessTokenRequest extends Request implements HasBody
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

    protected string $code;

    protected OAuthConfig $oauthConfig;

    public function __construct(string $code, OAuthConfig $oauthConfig)
    {
        $this->code = $code;
        $this->oauthConfig = $oauthConfig;
    }

    /**
     * Register the default data.
     *
     * @return array{
     *     grant_type: string,
     *     code: string,
     *     client_id: string,
     *     client_secret: string,
     *     redirect_uri: string,
     * }
     */
    public function defaultBody(): array
    {
        return [
            'grant_type' => 'authorization_code',
            'code' => $this->code,
            'client_id' => $this->oauthConfig->getClientId(),
            'client_secret' => $this->oauthConfig->getClientSecret(),
            'redirect_uri' => $this->oauthConfig->getRedirectUri(),
        ];
    }
}
