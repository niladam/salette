<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Contracts\Body\HasBody;
use Salette\Data\MultipartValue;
use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Traits\Body\HasMultipartBody;

class HasMultipartBodyRequest extends Request implements HasBody
{
    use HasMultipartBody;

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
            new MultipartValue('nickname', 'Sam', 'user.txt', ['X-Saloon' => 'Yee-haw!']),
        ];
    }
}
