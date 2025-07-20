<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Connector;

class CustomBaseUrlConnector extends Connector
{
    /**
     * Base URL
     */
    protected string $baseUrl = '';

    /**
     * Define the base URL of the API.
     */
    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Set a base URL
     *
     * @return CustomBaseUrlConnector
     */
    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }
}
