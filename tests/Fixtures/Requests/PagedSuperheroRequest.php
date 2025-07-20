<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;

class PagedSuperheroRequest extends Request
{
    public const METHOD = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/superheroes/per-page';
    }
}
