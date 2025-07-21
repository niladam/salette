<?php

declare(strict_types=1);

namespace App\Http\Integrations;

use Salette\Http\Connector;
use Salette\Traits\Plugins\AcceptsJson;

class FacadeTestConnector extends Connector
{
    use AcceptsJson;

    /**
     * Define the base URL of the API.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://jsonplaceholder.typicode.com';
    }

    /**
     * Define the base headers that will be applied in every request.
     *
     * @return string[]
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}
