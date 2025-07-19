<?php

declare(strict_types=1);

namespace Salette\Http\Middleware;

use Salette\Contracts\RequestMiddleware;
use Salette\Requests\PendingRequest;

class DelayMiddleware implements RequestMiddleware
{
    /**
     * Register a request middleware
     */
    public function __invoke(PendingRequest $pendingRequest): void
    {
        $delay = $pendingRequest->delay()->get() ?? 0;

        usleep($delay * 1000);
    }
}
