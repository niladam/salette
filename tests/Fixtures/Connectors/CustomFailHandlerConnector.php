<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Response;
use Salette\Http\Connector;
use Salette\Traits\Plugins\AcceptsJson;

class CustomFailHandlerConnector extends Connector
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
     * Determine if the request has failed
     */
    public function hasRequestFailed(Response $response): ?bool
    {
        return str_contains($response->body(), 'Error:');
    }
}
