<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Contracts\Authenticator;
use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Authenticators\PizzaAuthenticator;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Traits\Auth\RequiresAuth;

class DefaultPizzaAuthenticatorRequest extends Request
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

    public function defaultAuth(): ?Authenticator
    {
        return new PizzaAuthenticator('BBQ Chicken', 'Lemonade');
    }

    public function __construct($userId = null, $groupId = null)
    {
        //
    }
}
