<?php

declare(strict_types=1);

namespace Salette\Http\OAuth2;

use Salette\Contracts\Body\HasBody;
use Salette\Enums\Method;
use Salette\Helpers\OAuth2\OAuthConfig;
use Salette\Requests\Request;
use Salette\Traits\Body\HasFormBody;
use Salette\Traits\Plugins\AcceptsJson;

class GetClientCredentialsTokenRequest extends Request implements HasBody
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

    protected array $scopes = [];

    protected string $scopeSeparator = ' ';

    public function __construct(OAuthConfig $oauthConfig, array $scopes = [], string $scopeSeparator = ' ')
    {
        $this->oauthConfig = $oauthConfig;
        $this->scopes = $scopes;
        $this->scopeSeparator = $scopeSeparator;
    }

    /**
     * Register the default data.
     *
     * @return array{
     *     grant_type: string,
     *     client_id: string,
     *     client_secret: string,
     *     scope: string,
     * }
     */
    public function defaultBody(): array
    {
        return [
            'grant_type' => 'client_credentials',
            'client_id' => $this->oauthConfig->getClientId(),
            'client_secret' => $this->oauthConfig->getClientSecret(),
            'scope' => implode($this->scopeSeparator, array_merge($this->oauthConfig->getDefaultScopes(), $this->scopes)),
        ];
    }
}
