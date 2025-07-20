<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Connectors\TestConnector;

class QueryParameterRequest extends Request
{
    /**
     * Define the method that the request will use.
     *
     * @var string
     */
    public const METHOD = Method::GET;
    public string $endpoint = '/user';

    /**
     * The connector.
     */
    protected string $connector = TestConnector::class;

    /**
     * Constructor
     */
    public function __construct(string $endpoint = '/user')
    {
        $this->endpoint = $endpoint;
        //
    }

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return $this->endpoint;
    }

    protected function defaultQuery(): array
    {
        return [
            'per_page' => 100,
        ];
    }
}
