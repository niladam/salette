<?php

declare(strict_types=1);

namespace Salette\Traits\RequestProperties;

use Salette\Contracts\ArrayStore as ArrayStoreContract;
use Salette\Repositories\ArrayStore;

trait HasConfig
{
    /**
     * Request Config
     */
    protected ArrayStoreContract $config;

    /**
     * Access the config
     */
    public function config(): ArrayStoreContract
    {
        return $this->config ??= new ArrayStore($this->defaultConfig());
    }

    /**
     * Default Config
     *
     * @return array<string, mixed>
     */
    protected function defaultConfig(): array
    {
        return [];
    }
}
