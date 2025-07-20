<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Throwable;
use Salette\Http\Response;
use Salette\Http\Connector;
use Salette\Traits\Plugins\AcceptsJson;
use Salette\Tests\Fixtures\Exceptions\ConnectorRequestException;

class CustomExceptionConnector extends Connector
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
     * Customise the request exception handler
     */
    public function getRequestException(Response $response, $senderException): ?Throwable
    {
        return new ConnectorRequestException($response, 'Oh yee-naw.', 0, $senderException);
    }
}
