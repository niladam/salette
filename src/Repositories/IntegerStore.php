<?php

declare(strict_types=1);

namespace Salette\Repositories;

use Salette\Contracts\IntegerStore as IntegerStoreContract;
use Salette\Traits\Conditionable;

class IntegerStore implements IntegerStoreContract
{
    use Conditionable;

    protected ?int $data = null;

    public function __construct(?int $value = null)
    {
        $this->set($value);
    }

    public function set(?int $value): self
    {
        $this->data = $value;

        return $this;
    }

    public function get(): ?int
    {
        return $this->data;
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }
}
