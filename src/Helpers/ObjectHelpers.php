<?php

declare(strict_types=1);

namespace Salette\Helpers;

/**
 * @internal
 */
class ObjectHelpers
{
    /**
     * Get an item from an object or array using "dot" notation.
     *
     * @param  object|array<string, mixed>  $object
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($object, $key, $default = null)
    {
        $keys = explode('.', $key);

        foreach ($keys as $segment) {
            if (is_object($object) && isset($object->{$segment})) {
                $object = $object->{$segment};
            } elseif (is_array($object) && array_key_exists($segment, $object)) {
                $object = $object[$segment];
            } else {
                return $default;
            }
        }

        return $object;
    }
}
