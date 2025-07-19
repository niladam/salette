<?php

declare(strict_types=1);

namespace Salette\Traits\Plugins;

use Salette\Enums\PipeOrder;
use Salette\Http\Response;
use Salette\Requests\PendingRequest;

/**
 * @phpstan-ignore trait.unused
 */
trait AlwaysThrowOnErrors
{
    /**
     * Boot AlwaysThrowOnErrors Plugin
     */
    public static function bootAlwaysThrowOnErrors(PendingRequest $pendingRequest): void
    {
        // This middleware will simply use the "throw" method on the response
        // which will check if the connector/request deems the response as a
        // failure - if it does, it will throw a RequestException.

        $pendingRequest->middleware()->onResponse(
            static fn (Response $response) => $response->throw(),
            'alwaysThrowOnErrors',
            PipeOrder::last(),
        );
    }
}
