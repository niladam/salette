<?php

declare(strict_types=1);

namespace Salette\Helpers;

use Exception;

/**
 * @internal
 */
class StringHelpers
{
    /**
     * Determine if a given string matches a given pattern.
     *
     * @param  string|iterable<string>  $pattern
     */
    public static function matchesPattern($pattern, string $value): bool
    {
        if (! is_iterable($pattern)) {
            $pattern = [$pattern];
        }

        foreach ($pattern as $item) {
            $patternString = (string) $item;

            // If the given value is an exact match we can return true immediately.
            if ($patternString === $value) {
                return true;
            }

            $quoted = preg_quote($patternString, '#');

            // Convert wildcard asterisks into regex equivalents
            $regex = str_replace('\*', '.*', $quoted);

            if (preg_match('#^' . $regex . '\z#u', $value) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Begin a string with a single instance of a given value.
     */
    public static function start(string $value, string $prefix): string
    {
        $quoted = preg_quote($prefix, '/');

        return $prefix . preg_replace(
            '/^(?:' . $quoted . ')+/u',
            '',
            $value
        );
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @throws Exception
     */
    public static function random(int $length = 16): string
    {
        $string = '';

        while (mb_strlen($string) < $length) {
            $remaining = $length - mb_strlen($string);

            /** @var positive-int $remaining */
            $bytes = random_bytes($remaining);

            $string .= mb_substr(
                str_replace(['/', '+', '='], '', base64_encode($bytes)),
                0,
                $remaining
            );
        }

        return $string;
    }
}
