<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Contracts\Body\HasBody;
use Salette\Traits\Body\HasStreamBody;

class HasStreamBodyRequest extends Request implements HasBody
{
    use HasStreamBody;

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

    protected function defaultBody()
    {
        $temp = fopen('php://memory', 'rw');

        fwrite($temp, 'Howdy, Partner');

        rewind($temp);

        return $temp;
    }
}
