<?php

declare(strict_types=1);

namespace Salette\Contracts;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

interface BodyRepository
{
    /**
     * Set the raw data in the repository.
     *
     * @param  array<array-key, mixed>|mixed  $value
     */
    public function set($value): self;

    /**
     * Get the raw data in the repository.
     *
     * @return array<array-key, mixed>|mixed
     */
    public function all();

    /**
     * Determine if the repository is empty
     */
    public function isEmpty(): bool;

    /**
     * Determine if the repository is not empty
     */
    public function isNotEmpty(): bool;

    /**
     * Convert the body repository into a stream
     */
    public function toStream(StreamFactoryInterface $streamFactory): StreamInterface;
}
