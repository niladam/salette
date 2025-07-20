<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Contracts\Body\HasBody;
use Salette\Traits\Body\HasMultipartBody;

class MixedMultipartRequest extends Request implements HasBody
{
    use HasMultipartBody;

    public const METHOD = Method::POST;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/mixed-multipart';
    }
}
