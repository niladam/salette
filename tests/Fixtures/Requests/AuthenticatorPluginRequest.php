<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Plugins\AuthenticatorPlugin;

class AuthenticatorPluginRequest extends Request
{
    use AuthenticatorPlugin;

    /**
     * Define the method that the request will use.
     *
     * @var string
     */
    public const METHOD = Method::GET;

    /**
     * The connector.
     */
    protected string $connector = TestConnector::class;

    
    public function __construct($userId = null, $groupId = null)
    {
        //
    }

    
    public function resolveEndpoint(): string
    {
        return '/user';
    }
}
