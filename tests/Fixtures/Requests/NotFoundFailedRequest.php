<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Http\Response;
use Salette\Requests\Request;
use Salette\Traits\Plugins\AlwaysThrowOnErrors;

class NotFoundFailedRequest extends Request
{
    use AlwaysThrowOnErrors;

    /**
     * Define the HTTP method.
     */
    public const METHOD = Method::GET;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/not-found';
    }

    /**
     * Determine if the request has failed
     */
    public function hasRequestFailed(Response $response): ?bool
    {
        if ($response->status() === 404) {
            return false;
        }

        return $response->serverError() || $response->clientError();
    }
}
