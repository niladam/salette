<?php

declare(strict_types=1);

namespace Salette\Traits\Plugins;

use Salette\Requests\PendingRequest;

trait AcceptsJson
{
    /**
     * Boot AcceptsJson Plugin
     */
    public static function bootAcceptsJson(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('Accept', 'application/json');
    }
}
