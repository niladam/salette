<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Requests\Request;
use Salette\Http\Connector;
use Salette\Traits\Plugins\AcceptsJson;

class WithBootConnector extends Connector
{
    use AcceptsJson;

    /**
     * Define the base url of the api.
     */
    public function resolveBaseUrl(): string
    {
        return apiUrl();
    }

    public function boot(Request $request)
    {
        $this->addHeader('X-Connector-Boot-Header', 'Howdy!');
        $this->addHeader('X-Connector-Request-Class', get_class($request));
    }
}
