<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Requests\Request;

class HeaderErrorRetryRequest extends RetryUserRequest
{
    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/header-error';
    }
}
