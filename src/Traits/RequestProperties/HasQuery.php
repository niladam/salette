<?php

declare(strict_types=1);

namespace Salette\Traits\RequestProperties;

use Salette\Contracts\ArrayStore as ArrayStoreContract;
use Salette\Repositories\ArrayStore;

trait HasQuery
{
    /**
     * Request Query Parameters
     */
    protected ArrayStoreContract $query;

    /**
     * Access the query parameters
     */
    public function query(): ArrayStoreContract
    {
        return $this->query ??= new ArrayStore($this->defaultQuery());
    }

    /**
     * Default Query Parameters
     *
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        return [];
    }
}
