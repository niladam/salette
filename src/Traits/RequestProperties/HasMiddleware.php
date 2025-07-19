<?php

declare(strict_types=1);

namespace Salette\Traits\RequestProperties;

use Salette\Helpers\MiddlewarePipeline;

trait HasMiddleware
{
    /**
     * Middleware Pipeline
     */
    protected MiddlewarePipeline $middlewarePipeline;

    /**
     * Access the middleware pipeline
     */
    public function middleware(): MiddlewarePipeline
    {
        return $this->middlewarePipeline ??= new MiddlewarePipeline();
    }
}
