<?php

declare(strict_types=1);

namespace Salette\Enums;

use Salette\Exceptions\InvalidHttpMethod;

final class Method
{
    public const GET = 'GET';

    public const HEAD = 'HEAD';

    public const POST = 'POST';

    public const PUT = 'PUT';

    public const PATCH = 'PATCH';

    public const DELETE = 'DELETE';

    public const OPTIONS = 'OPTIONS';

    public const CONNECT = 'CONNECT';

    public const TRACE = 'TRACE';

    /**
     * Return all allowed HTTP methods.
     *
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::GET,
            self::HEAD,
            self::POST,
            self::PUT,
            self::PATCH,
            self::DELETE,
            self::OPTIONS,
            self::CONNECT,
            self::TRACE,
        ];
    }

    /**
     * Check if a string is a valid HTTP method.
     */
    public static function isValid(string $method): bool
    {
        return in_array($method, self::values(), true);
    }

    public static function validate(string $method): void
    {
        if ($method === '') {
            throw new InvalidHttpMethod(
                'Your request is missing a HTTP method. '
                . 'Please define a property like [ public const METHOD = Method::GET; ]'
            );
        }

        if (! Method::isValid($method)) {
            throw new InvalidHttpMethod(
                sprintf(
                    'Invalid HTTP method [%s]. Must be one of: %s',
                    $method,
                    implode(', ', Method::values())
                )
            );
        }
    }
}
