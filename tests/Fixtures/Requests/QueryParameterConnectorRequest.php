<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Connectors\QueryParameterConnector;

class QueryParameterConnectorRequest extends Request
{
    /**
     * Define the method that the request will use.
     *
     * @var string
     */
    public const METHOD = Method::GET;

    /**
     * The connector.
     */
    protected string $connector = QueryParameterConnector::class;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }

    protected function defaultQuery(): array
    {
        return [
            'include' => 'user',
        ];
    }
}
