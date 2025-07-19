<?php

declare(strict_types=1);

namespace Salette\Traits\Body;

use Salette\Contracts\Body\HasBody;
use Salette\Exceptions\BodyException;
use Salette\Requests\PendingRequest;

/**
 * @phpstan-ignore trait.unused
 */
trait ChecksForHasBody
{
    /**
     * Check if the request or connector has the WithBody class.
     *
     * @throws BodyException
     */
    public function bootChecksForHasBody(PendingRequest $pendingRequest): void
    {
        if ($pendingRequest->getRequest() instanceof HasBody || $pendingRequest->getConnector() instanceof HasBody) {
            return;
        }

        throw new BodyException(
            sprintf(
                'You have added a body trait without implementing `%s` on your request or connector.',
                HasBody::class
            )
        );
    }
}
