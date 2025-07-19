<?php

declare(strict_types=1);

namespace Salette\Http\OAuth2;

use Salette\Contracts\Body\HasBody;
use Salette\Enums\Method;
use Salette\Helpers\OAuth2\OAuthConfig;
use Salette\Requests\Request;
use Salette\Traits\Body\HasFormBody;
use Salette\Traits\Plugins\AcceptsJson;

class GetUserRequest extends Request implements HasBody
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
        return $this->oauthConfig->getUserEndpoint();
    }

    protected OAuthConfig $oauthConfig;

    public function __construct(OAuthConfig $oauthConfig)
    {
        $this->oauthConfig = $oauthConfig;
    }
}
