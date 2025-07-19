<?php

declare(strict_types=1);

namespace Salette\Traits;

use BadMethodCallException;
use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Many thanks to Spatie for building this excellent trait.
 *
 * @see https://github.com/spatie/macroable
 */
trait Macroable
{
    /**
     * Macros stored
     *
     * @var array<object|callable>
     */
    protected static array $macros = [];

    /**
     * Create a macro
     *
     * @param  object|callable  $macro
     */
    public static function macro(string $name, $macro): void
    {
        static::$macros[$name] = $macro;
    }

    /**
     * Add a mixin
     *
     * @param  object|class-string  $mixin  Either an instance whose methods return macros, or a class-name to
     *                                      instantiate
     *
     * @throws ReflectionException
     */
    public static function mixin($mixin): void
    {
        $className = is_object($mixin) ? get_class($mixin) : $mixin;
        $instance = is_object($mixin) ? $mixin : new $className();

        $reflection = new ReflectionClass($className);
        $methods = $reflection->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );

        foreach ($methods as $method) {
            $method->setAccessible(true);
            $macro = $method->invoke($instance);
            static::macro($method->getName(), $macro);
        }
    }

    /**
     * Check if we have a macro
     */
    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[$name]);
    }

    /**
     * Handle a static call
     *
     * @param  array<string,mixed>  $parameters
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public static function __callStatic(string $method, array $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        $macro = static::$macros[$method];
        if ($macro instanceof Closure) {
            $callback = Closure::bind($macro, null, static::class);
        } else {
            $callback = $macro;
        }

        /** @var callable $callback */
        return call_user_func_array($callback, $parameters);
    }

    /**
     * Handle a method call
     *
     * @param  array<string,mixed>  $parameters
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function __call(string $method, array $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        $macro = static::$macros[$method];
        if ($macro instanceof Closure) {
            $callback = $macro->bindTo($this, static::class);
        } else {
            $callback = $macro;
        }

        /** @var callable $callback */
        return call_user_func_array($callback, $parameters);
    }
}
