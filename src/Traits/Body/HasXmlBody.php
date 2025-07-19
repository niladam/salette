<?php

declare(strict_types=1);

namespace Salette\Traits\Body;

use Salette\Requests\PendingRequest;

/**
 * @phpstan-ignore trait.unused
 */
trait HasXmlBody
{
    use HasStringBody;

    /**
     * Boot the plugin
     */
    public function bootHasXmlBody(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('Content-Type', 'application/xml');
    }
}
