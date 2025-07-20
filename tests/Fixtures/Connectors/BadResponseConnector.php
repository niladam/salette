<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Response;
use Salette\Http\Connector;
use Salette\Traits\Plugins\AcceptsJson;

class BadResponseConnector extends Connector
{
    use AcceptsJson;

    /**
     * Define the base url of the api.
     */
    public function resolveBaseUrl(): string
    {
        return apiUrl();
    }

    /**
     * Check if we should throw an exception
     */
    public function shouldThrowRequestException(Response $response): bool
    {
        return strpos($response->body(), 'Error:') !== false;
    }
}
