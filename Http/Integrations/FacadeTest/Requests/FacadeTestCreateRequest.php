<?php

declare(strict_types=1);

namespace App\Http\Integrations\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Traits\Body\HasJsonBody;
use Salette\Contracts\Body\HasBody;

class FacadeTestCreateRequest extends Request implements HasBody
{
    use HasJsonBody;

    public const METHOD = Method::POST;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/posts';
    }

    /**
     * Define the default body for the request.
     *
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'title' => 'Sample Post',
            'body' => 'This is a sample post created via Salette',
            'userId' => 1,
        ];
    }
}
