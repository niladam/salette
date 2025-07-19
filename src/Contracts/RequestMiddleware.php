<?php

declare(strict_types=1);

namespace Salette\Contracts;

use Salette\Requests\PendingRequest;

interface RequestMiddleware
{
    public function __invoke(PendingRequest $pendingRequest);
}
