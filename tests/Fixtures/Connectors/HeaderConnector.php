<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Connector;
use Salette\Traits\Plugins\AcceptsJson;

class HeaderConnector extends Connector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return apiUrl();
    }

    public function defaultHeaders(): array
    {
        return [
            'X-Connector-Header' => 'Sam',
        ];
    }

    public function defaultConfig(): array
    {
        return [
            'http_errors' => false,
        ];
    }
}
