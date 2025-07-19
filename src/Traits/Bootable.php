<?php

declare(strict_types=1);

namespace Salette\Traits;

use Salette\Requests\PendingRequest;

trait Bootable
{
    /**
     * Handle the boot lifecycle hook
     */
    public function boot(PendingRequest $pendingRequest): void
    {
        //
    }
}
