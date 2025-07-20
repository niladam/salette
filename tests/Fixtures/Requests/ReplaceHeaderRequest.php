<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Connectors\HeaderConnector;
use Salette\Traits\Body\HasJsonBody;

class ReplaceHeaderRequest extends Request
{
    use HasJsonBody;

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
    protected string $connector = HeaderConnector::class;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }

    public function defaultHeaders(): array
    {
        return [
            'X-Connector-Header' => 'Howdy',
        ];
    }

    public function defaultData(): array
    {
        return [
            'foo' => 'bar',
        ];
    }
}
