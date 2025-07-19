<?php

declare(strict_types=1);

namespace Salette\Data;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

class MultipartValue
{
    public string $name;

    /**
     * @var mixed
     */
    public $value;

    public ?string $filename;

    public array $headers;

    public function __construct(string $name, $value, ?string $filename = null, array $headers = [])
    {
        if (
            ! $value instanceof StreamInterface
            && ! is_resource($value)
            && ! is_string($value)
            && ! is_numeric($value)
        ) {
            throw new InvalidArgumentException(sprintf(
                'The value property must be either a %s, resource, string or numeric.',
                StreamInterface::class
            ));
        }

        $this->name = $name;
        $this->value = $value;
        $this->filename = $filename;
        $this->headers = $headers;
    }
}
