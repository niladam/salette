<?php

declare(strict_types=1);

namespace Salette\Traits;

use Salette\Support\Helpers;

trait Conditionable
{
    /**
     * Invoke a callable when a given value returns a truthy value.
     *
     * @param  \Closure():mixed|mixed  $value  A value or a zero-arg closure that returns a value.
     * @param  callable  $callback  fn($this, $value): void
     * @param  callable|null  $default  fn($this, $value): void
     * @return $this
     */
    public function when($value, callable $callback, ?callable $default = null): self
    {
        $value = Helpers::value($value, $this);

        if ($value) {
            $callback($this, $value);

            return $this;
        }

        if ($default !== null) {
            $default($this, $value);
        }

        return $this;
    }

    /**
     * Invoke a callable when a given value returns a falsy value.
     *
     * @param  \Closure():mixed|mixed  $value  A value or a zero-arg closure that returns a value.
     * @param  callable  $callback  fn($this, $value): void
     * @param  callable|null  $default  fn($this, $value): void
     * @return $this
     */
    public function unless($value, callable $callback, ?callable $default = null): self
    {
        $value = Helpers::value($value, $this);

        if (! $value) {
            $callback($this, $value);

            return $this;
        }

        if ($default !== null) {
            $default($this, $value);
        }

        return $this;
    }
}
