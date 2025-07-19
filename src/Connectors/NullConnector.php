<?php

declare(strict_types=1);

namespace Salette\Connectors;

use Salette\Http\Connector;

class NullConnector extends Connector
{
    /**
     * Define the base URL of the API.
     */
    public function resolveBaseUrl(): string
    {
        return '';
    }
}
