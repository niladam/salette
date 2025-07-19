<?php

declare(strict_types=1);

namespace Salette\Senders;

use GuzzleHttp\Psr7\MultipartStream;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Salette\Contracts\MultipartBodyFactory;
use Salette\Data\MultipartValue;

class GuzzleMultipartBodyFactory implements MultipartBodyFactory
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
    ): StreamInterface {
        $elements = array_map(static function (MultipartValue $value) {
            return [
                'name' => $value->name,
                'filename' => $value->filename,
                'contents' => $value->value,
                'headers' => $value->headers,
            ];
        }, $multipartValues);

        return new MultipartStream($elements, $boundary);
    }
}
