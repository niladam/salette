<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Contracts\Body\HasBody;
use Salette\Traits\Body\HasFormBody;

class HasFormBodyRequest extends Request implements HasBody
{
    use HasFormBody;

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

    /**
     * Default Body
     *
     * @return string[]
     */
    protected function defaultBody(): array
    {
        return [
            'name' => 'Sam',
            'catchphrase' => 'Yeehaw!',
        ];
    }
}
