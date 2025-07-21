<?php

declare(strict_types=1);

namespace Salette\Repositories;

use Salette\Contracts\BodyRepository;
use Salette\Traits\Body\CreatesStreamFromString;
use Salette\Traits\Conditionable;

class StringBodyRepository implements BodyRepository
{
    use Conditionable;
    use CreatesStreamFromString;

    /**
     * Repository Data
     */
    protected ?string $data = null;

    public function __construct(?string $value = null)
    {
        $this->set($value);
    }

    /**
     * Set a value inside the repository
     *
     * @param  string|null  $value
     * @return $this
     */
    public function set($value): self
    {
        $this->data = $value;

        return $this;
    }

    /**
     * Retrieve all in the repository
     */
    public function all(): ?string
    {
        return $this->data;
    }

    /**
     * Determine if the repository is empty
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Determine if the repository is not empty
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * Convert the repository into a string
     */
    public function __toString(): string
    {
        return $this->all() ?? '';
    }
}
