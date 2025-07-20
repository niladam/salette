<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Traits\Request\HasConnector;
use Salette\Tests\Fixtures\Connectors\TestConnector;

class HasConnectorUserRequest extends Request
{
    use HasConnector;

    /**
     * Define connector
     */
    protected string $connector = TestConnector::class;

    /**
     * Define the HTTP method.
     *
     * @var string
     */
    public const METHOD = Method::GET;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }
}
