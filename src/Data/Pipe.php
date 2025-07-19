<?php

declare(strict_types=1);

namespace Salette\Data;

use Closure;
use Salette\Enums\PipeOrder;

class Pipe
{
    /**
     * The callable inside the pipe
     */
    public Closure $callable;

    public ?string $name = null;

    public ?PipeOrder $order = null;

    public function __construct(
        callable $callable,
        ?string $name = null,
        ?PipeOrder $order = null
    ) {
        $this->order = $order;
        $this->name = $name;
        $this->callable = Closure::fromCallable($callable);
    }
}
