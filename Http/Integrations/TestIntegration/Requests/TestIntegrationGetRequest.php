<?php

declare(strict_types=1);

namespace App\Http\Integrations\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;

class TestIntegrationGetRequest extends Request
{
    public const METHOD = Method::GET;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/posts/1';
    }
}
