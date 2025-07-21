<?php

declare(strict_types=1);

namespace Salette\Contracts;

interface ArrayStore
{
    /**
     * Retrieve all the items.
     *
     * @return array<string, mixed>
     */
    public function all(): array;

    /**
     * Retrieve a single item.
     *
     * @param  mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Overwrite the entire repository's contents.
     *
     * @param  array<string, mixed> $data
     * @return $this
     */
    public function set(array $data): self;

    /**
     * Merge in other arrays.
     *
     * @param  array<string, mixed> ...$arrays
     * @return $this
     */
    public function merge(array ...$arrays): self;

    /**
     * Add an item to the repository.
     *
     * @param  mixed $value
     * @return $this
     */
    public function add(string $key, $value): self;

    /**
     * Remove an item from the store.
     *
     * @return $this
     */
    public function remove(string $key): self;

    /**
     * Determine if the store is empty.
     */
    public function isEmpty(): bool;

    /**
     * Determine if the store is not empty.
     */
    public function isNotEmpty(): bool;
}
