<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Connectors\HeaderConnector;

class HeaderRequest extends Request
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
    protected string $connector = HeaderConnector::class;

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
            'X-Custom-Header' => 'Howdy',
        ];
    }

    protected function defaultConfig(): array
    {
        return [
            'timeout' => 5,
        ];
    }
}
