<?php

declare(strict_types=1);

namespace Salette\Repositories;

use InvalidArgumentException;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Salette\Contracts\BodyRepository;
use Salette\Traits\Conditionable;

class StreamBodyRepository implements BodyRepository
{
    use Conditionable;

    /**
     * The stream body
     *
     * @var StreamInterface|resource|null
     */
    protected $stream = null;

    /**
     * @param  StreamInterface|resource|null  $value
     */
    public function __construct($value = null)
    {
        $this->set($value);
    }

    /**
     * Set a value inside the repository
     *
     * @param  StreamInterface|resource|null  $value
     * @return $this
     */
    public function set($value): self
    {
        if (isset($value) && ! $value instanceof StreamInterface && ! is_resource($value)) {
            throw new InvalidArgumentException(
                'The value must a resource or be an instance of ' . StreamInterface::class
            );
        }

        $this->stream = $value;

        return $this;
    }

    /**
     * Retrieve the stream from the repository
     */
    public function all(): array
    {
        return $this->stream;
    }

    /**
     * Retrieve the stream from the repository
     *
     * Alias of "all" method.
     */
    public function get()
    {
        return $this->all();
    }

    /**
     * Determine if the repository is empty
     */
    public function isEmpty(): bool
    {
        return is_null($this->stream);
    }

    /**
     * Determine if the repository is not empty
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
        $stream = $this->stream;

        return $stream instanceof StreamInterface ? $stream : $streamFactory->createStreamFromResource($stream);
    }
}
