<?php

declare(strict_types=1);

namespace Salette\Helpers;

use ArrayAccess;
use Salette\Support\Helpers;

use function is_string;

/**
 * @internal
 */
class ArrayHelpers
{
    /**
     * Determine whether the given value is array accessible.
     *
     * @phpstan-assert-if-true array<array-key, mixed>|ArrayAccess<array-key, mixed> $value
     *
     * @param  mixed  $value
     */
    private static function accessible($value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  array<array-key, mixed>|ArrayAccess<array-key, mixed>  $array
     * @param  string|int|float  $key
     */
    private static function exists($array, $key): bool
    {
        if (is_float($key)) {
            $key = (string) $key;
        }

        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array<array-key, mixed>  $array
     * @param  string|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get(array $array, $key, $default = null)
    {
        if ($key === null) {
            return $array;
        }

        if (self::exists($array, $key)) {
            return $array[$key];
        }

        if (! is_string($key) || strpos($key, '.') === false) {
            return $array[$key] ?? Helpers::value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (self::accessible($array) && self::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return Helpers::value($default);
            }
        }

        return $array;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array<array-key, mixed>  &$array
     * @param  string|int|null  $key
     * @param  mixed  $value
     * @return array<array-key, mixed>
     */
    public static function set(array &$array, $key, $value)
    {
        if ($key === null) {
            $array = $value;

            return $array;
        }

        $keys = explode('.', (string) $key);

        foreach ($keys as $i => $segment) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            if (! isset($array[$segment]) || ! is_array($array[$segment])) {
                $array[$segment] = [];
            }

            $array = &$array[$segment];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}
