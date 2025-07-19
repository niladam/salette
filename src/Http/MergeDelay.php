<?php

declare(strict_types=1);

namespace Salette\Http;

use Salette\Requests\PendingRequest;

class MergeDelay
{
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $connector = $pendingRequest->getConnector();
        $request = $pendingRequest->getRequest();

        $pendingRequest->delay()->set($connector->delay()->get());

        if ($request->delay()->isNotEmpty()) {
            $pendingRequest->delay()->set($request->delay()->get());
        }

        return $pendingRequest;
    }
}
