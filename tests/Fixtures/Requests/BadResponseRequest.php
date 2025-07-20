<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Http\Response;

class BadResponseRequest extends Request
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
     * Check if we should throw an exception
     */
    public function shouldThrowRequestException(Response $response): bool
    {
        return str_contains($response->body(), 'Yee-naw:');
    }
}
