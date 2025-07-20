<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Http\Response;
use Salette\Requests\Request;

class AlwaysHasFailureRequest extends Request
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
     * Determines if there is always a failure
     */
    public function shouldThrowRequestException(Response $response): bool
    {
        return true;
    }
}
