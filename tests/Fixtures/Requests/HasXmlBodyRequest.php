<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Contracts\Body\HasBody;
use Salette\Traits\Body\HasXmlBody;

class HasXmlBodyRequest extends Request implements HasBody
{
    use HasXmlBody;

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
     */
    protected function defaultBody()
    {
        return '<p>Howdy</p>';
    }
}
