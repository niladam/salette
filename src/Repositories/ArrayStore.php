<?php

declare(strict_types=1);

namespace Salette\Repositories;

use Salette\Contracts\ArrayStore as ArrayStoreContract;
use Salette\Support\Helpers;
use Salette\Traits\Conditionable;

class ArrayStore implements ArrayStoreContract
{
    use Conditionable;

    /**
     * The repository's store.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Retrieve all the items.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Retrieve a single item.
     *
     * @param  mixed|null  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Overwrite the entire repository.
     *
     * @param  array<string, mixed>  $data
     */
    public function set(array $data): ArrayStoreContract
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Merge in other arrays.
     *
     * @param  array<string, mixed>  ...$arrays
     * @return $this
     */
    public function merge(array ...$arrays): ArrayStoreContract
    {
        $this->data = array_merge($this->data, ...$arrays);

        return $this;
    }

    /**
     * Add an item to the repository.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function add(string $key, $value): ArrayStoreContract
    {
        $this->data[$key] = Helpers::value($value);

        return $this;
    }

    /**
     * Remove an item from the store.
     *
     * @return $this
     */
    public function remove(string $key): ArrayStoreContract
    {
        unset($this->data[$key]);

        return $this;
    }

    /**
     * Determine if the store is empty.
     *
     * @phpstan-assert-if-false non-empty-array<array-key, mixed> $this->data
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Determine if the store is not empty.
     *
     * @phpstan-assert-if-true non-empty-array<array-key, mixed> $this->data
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }
}
