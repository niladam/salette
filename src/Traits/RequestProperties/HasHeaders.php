<?php

declare(strict_types=1);

namespace Salette\Traits\RequestProperties;

use Salette\Contracts\ArrayStore as ArrayStoreContract;
use Salette\Repositories\ArrayStore;

trait HasHeaders
{
    /**
     * Request Headers
     */
    protected ArrayStoreContract $headers;

    /**
     * Access the headers
     */
    public function headers(): ArrayStoreContract
    {
        return $this->headers ??= new ArrayStore($this->defaultHeaders());
    }

    /**
     * Default Request Headers
     *
     * @return array<string, mixed>
     */
    protected function defaultHeaders(): array
    {
        return [];
    }
}
