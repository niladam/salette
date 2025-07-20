<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Traits\Body\HasStringBody;
use Salette\Contracts\Body\HasBody as HasBodyContract;

class HasStringBodyRequest extends Request implements HasBodyContract
{
    use HasStringBody;

    /**
     * Define the method that the request will use.
     */
    public const METHOD = Method::GET;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }

    protected function defaultBody(): ?string
    {
        return 'name: Sam';
    }
}
