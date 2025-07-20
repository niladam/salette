<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Connectors\TestConnector;

class DefaultEndpointRequest extends Request
{
    /**
     * Define the method that the request will use.
     *
     * @var string|null
     */
     public const METHOD = Method::POST;

    /**
     * The connector.
     *
     * @var string|null
     */
    protected string $connector = TestConnector::class;

    
    public function resolveEndpoint(): string
    {
        return '';
    }
}
