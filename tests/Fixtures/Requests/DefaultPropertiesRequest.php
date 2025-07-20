<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Connectors\TestConnector;

class DefaultPropertiesRequest extends Request
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
    protected string $connector = TestConnector::class;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }

    protected function defaultHeaders(): array
    {
        return [
            'X-Favourite-Artist' => 'Luke Combs',
        ];
    }

    protected function defaultQuery(): array
    {
        return [
            'format' => 'json',
        ];
    }

    protected function defaultData(): array
    {
        return [
            'song' => 'Call Me',
        ];
    }

    protected function defaultConfig(): array
    {
        return [
            'debug' => true,
        ];
    }
}
