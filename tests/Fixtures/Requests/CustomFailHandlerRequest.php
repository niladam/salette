<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Http\Response;

class CustomFailHandlerRequest extends Request
{
    /**
     * Define the HTTP method.
     *
     * @var string
     */
    public const METHOD = Method::GET;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }

    /**
     * Determine if the request has failed
     */
    public function hasRequestFailed(Response $response): ?bool
    {
        return strpos($response->body(), 'Yee-naw:') !== false;
    }
}
