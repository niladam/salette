<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Contracts\Authenticator;
use Salette\Traits\Auth\RequiresAuth;
use Salette\Auth\TokenAuthenticator;
use Salette\Tests\Fixtures\Connectors\TestConnector;

class DefaultAuthenticatorRequest extends Request
{
    use RequiresAuth;

    /**
     * Define the method that the request will use.
     *
     * @var string|null
     */
    public const METHOD = Method::GET;

    /**
     * The connector.
     *
     * @var string|null
     */
    protected string $connector = TestConnector::class;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }

    /**
     * Provide default authentication.
     */
    protected function defaultAuth(): ?Authenticator
    {
        return new TokenAuthenticator('yee-haw-request');
    }

    public function __construct($userId = null, $groupId = null)
    {
        //
    }
}
