<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Http\SoloRequest;

class SoloUserRequest extends SoloRequest
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
        return 'https://tests.saloon.dev/api/user';
    }
}
