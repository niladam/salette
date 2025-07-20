<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Response;
use Salette\Http\Connector;
use Salette\Traits\Plugins\AcceptsJson;
use Salette\Tests\Fixtures\Data\ApiResponse;

class DtoConnector extends Connector
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
     * Define the base headers that will be applied in every request.
     *
     * @return string[]
     */
    public function defaultHeaders(): array
    {
        return [];
    }

    /**
     * Create DTO from Response
     */
    public function createDtoFromResponse(Response $response)
    {
        return ApiResponse::fromSalette($response);
    }
}
