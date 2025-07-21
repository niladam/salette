<?php

declare(strict_types=1);

namespace Salette\Support;

use Closure;
use ReflectionClass;
use ReflectionException;
use Salette\Exceptions\InvalidBootableTraitException;
use Salette\Http\Connector;
use Salette\Requests\PendingRequest;
use Salette\Requests\Request;

class Helpers
{
    /**
     * Recursively gather all traits used by a class and its parents.
     *
     * @param  object|class-string  $class  An object instance or fully-qualified class name
     * @return string[] List of trait FQCNs
     */
    public static function classUsesRecursive($class): array
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $results = [];

        $parents = class_parents($class) ?: [];
        foreach (array_reverse($parents) + [$class => $class] as $c) {
            $results += static::traitUsesRecursive($c);
        }

        return array_unique($results);
    }

    /**
     * @param  class-string  $trait  Fully-qualified trait name
     * @return string[] List of trait FQCNs
     */
    public static function traitUsesRecursive(string $trait): array
    {
        $traits = class_uses($trait) ?: [];

        foreach ($traits as $t) {
            $traits += static::traitUsesRecursive($t);
        }

        return $traits;
    }

    /**
     * If $value is a Closure, invoke it with $args; otherwise return it directly.
     *
     * @param  mixed  $value
     * @param  mixed  ...$args
     * @return mixed
     */
    public static function value($value, ...$args)
    {
        return $value instanceof Closure
            ? $value(...$args)
            : $value;
    }

    /**
     * Determine whether $class is the same as or a subclass of $subclass.
     *
     * @param  class-string  $class  Fully-qualified class name to test
     * @param  class-string  $subclass  Fully-qualified parent or interface name
     *
     * @throws ReflectionException
     */
    public static function isSubclassOf(string $class, string $subclass): bool
    {
        if ($class === $subclass) {
            return true;
        }

        return (new ReflectionClass($class))->isSubclassOf($subclass);
    }

    /**
     * If the given $resource (Connector or Request) has a static bootTraitName() method,
     * call it with the PendingRequest.
     *
     * @param  Connector|Request  $resource
     * @param  string  $trait  Fully-qualified trait name to
     *                         “boot”
     *
     * @throws ReflectionException|InvalidBootableTraitException
     */
    public static function bootPlugin(
        PendingRequest $pendingRequest,
        $resource,
        string $trait
    ): void {
        if (! trait_exists($trait)) {
            throw new InvalidBootableTraitException("Tried booting the trait {$trait} but it was not found");
        }

        $reflection = new ReflectionClass($trait);
        $method = 'boot' . $reflection->getShortName();

        if (method_exists($resource, $method)) {
            $resource->{$method}($pendingRequest);
        }
    }
}
