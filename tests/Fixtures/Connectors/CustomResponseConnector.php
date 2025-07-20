<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Connector;
use Salette\Tests\Fixtures\Responses\CustomResponse;

class CustomResponseConnector extends Connector
{
    protected ?string $response = CustomResponse::class;

    /**
     * Define the base url of the api.
     */
    public function resolveBaseUrl(): string
    {
        return apiUrl();
    }
}
