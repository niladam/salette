<?php

declare(strict_types=1);

namespace Salette\Contracts;

interface IntegerStore
{
    /**
     * Set a value inside the repository
     */
    public function set(?int $value): self;

    /**
     * Retrieve all in the repository
     */
    public function get(): ?int;

    /**
     * Determine if the repository is empty
     */
    public function isEmpty(): bool;

    /**
     * Determine if the repository is not empty
     */
    public function isNotEmpty(): bool;
}
