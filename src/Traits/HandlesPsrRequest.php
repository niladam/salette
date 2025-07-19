<?php

declare(strict_types=1);

namespace Salette\Traits;

use Psr\Http\Message\RequestInterface;
use Salette\Requests\PendingRequest;

trait HandlesPsrRequest
{
    /**
     * Handle the PSR request before it is sent
     */
    public function handlePsrRequest(RequestInterface $request, PendingRequest $pendingRequest): RequestInterface
    {
        return $request;
    }
}
