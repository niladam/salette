<?php

declare(strict_types=1);

namespace Salette\Traits\Body;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

trait CreatesStreamFromString
{
    /**
     * Convert the body repository into a stream
     */
    public function toStream(StreamFactoryInterface $streamFactory): StreamInterface
    {
        return $streamFactory->createStream((string) $this);
    }
}
