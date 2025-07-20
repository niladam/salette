<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Connectors\CustomBaseUrlConnector;

class CustomEndpointRequest extends Request
{
    /**
     * Connector
     */
    protected string $connector = CustomBaseUrlConnector::class;

    /**
     * Endpoint
     */
    protected string $endpoint = '';

    /**
     * Method
     *
     * @var string
     */
    public const METHOD = Method::GET;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Set an endpoint
     */
    public function setEndpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }
}
