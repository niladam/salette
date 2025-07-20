<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Plugins\HasTestHandler;
use Salette\Tests\Fixtures\Connectors\CustomResponseConnector;

class CustomResponseConnectorRequest extends Request
{
    use HasTestHandler;

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
    protected string $connector = CustomResponseConnector::class;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }
}
