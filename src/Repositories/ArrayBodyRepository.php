<?php

declare(strict_types=1);

namespace Salette\Repositories;

use InvalidArgumentException;
use LogicException;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Salette\Contracts\Body\MergeableBody;
use Salette\Contracts\BodyRepository;
use Salette\Traits\Conditionable;

class ArrayBodyRepository implements BodyRepository, MergeableBody
{
    use Conditionable;

    /**
     * Repository Data
     *
     * @var array<array-key, mixed>
     */
    protected array $data = [];

    /**
     * Constructor
     *
     * @param array<array-key, mixed> $value
     */
    public function __construct(array $value = [])
    {
        $this->set($value);
    }

    /**
     * Set a value inside the repository
     *
     * @param  array<array-key, mixed> $value
     * @return $this
     */
    public function set($value): self
    {
        if (! is_array($value)) {
            throw new InvalidArgumentException('The value must be an array');
        }

        $this->data = $value;

        return $this;
    }

    /**
     * Merge another array into the repository
     *
     * @param  array<array-key, mixed> ...$arrays
     * @return $this
     */
    public function merge(array ...$arrays): self
    {
        $this->data = array_merge($this->data, ...$arrays);

        return $this;
    }

    /**
     * Add an element to the repository.
     *
     * @param  array-key|null $key
     * @return $this
     */
    public function add($key = null, $value = null): self
    {
        isset($key)
            ? $this->data[$key] = $value
            : $this->data[] = $value;

        return $this;
    }

    /**
     * Get the raw data in the repository.
     *
     * @return array<mixed, mixed>
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Get a specific key of the array
     *
     * Alias of `all()`.
     *
     * @param  array-key|null $key
     * @return ($key is null ? array<array-key, mixed> : mixed)
     */
    public function get($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->all();
        }

        return $this->all()[$key] ?? $default;
    }

    /**
     * Remove an item from the repository.
     *
     * @param  array-key $key
     * @return $this
     */
    public function remove($key): self
    {
        unset($this->data[$key]);

        return $this;
    }

    /**
     * Determine if the repository is empty
     *
     * @phpstan-assert-if-false non-empty-array<array-key, mixed> $this->data
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Determine if the repository is not empty
     *
     * @phpstan-assert-if-true non-empty-array<array-key, mixed> $this->data
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * Convert the body repository into a stream
     */
    public function toStream(StreamFactoryInterface $streamFactory): StreamInterface
    {
        throw new LogicException('Unable to create a stream directly from an array body repository.');
    }
}
