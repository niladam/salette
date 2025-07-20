<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Requests\Request;

class MissingMethodRequest extends Request
{
    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }
}
