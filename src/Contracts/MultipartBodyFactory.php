<?php

declare(strict_types=1);

namespace Salette\Contracts;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Salette\Data\MultipartValue;

interface MultipartBodyFactory
{
    /**
     * Create a multipart body
     *
     * @param  array<MultipartValue>  $multipartValues
     */
    public function create(
        StreamFactoryInterface $streamFactory,
        array $multipartValues,
        string $boundary
    ): StreamInterface;
}
