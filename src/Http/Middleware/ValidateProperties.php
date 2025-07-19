<?php

declare(strict_types=1);

namespace Salette\Http\Middleware;

use Salette\Contracts\RequestMiddleware;
use Salette\Exceptions\InvalidHeaderException;
use Salette\Requests\PendingRequest;

class ValidateProperties implements RequestMiddleware
{
    /**
     * Validate the properties on the request before it is sent
     *
     * @throws InvalidHeaderException
     */
    public function __invoke(PendingRequest $pendingRequest): void
    {
        // Validate that each header provided has a string key

        foreach ($pendingRequest->headers()->all() as $key => $unused) {
            /** @phpstan-ignore-next-line */
            if (! is_string($key)) {
                throw new InvalidHeaderException(
                    'One or more of the headers are invalid.' .
                    'Make sure to use the header name as the key. ' .
                    'For example: [\'Content-Type\' => \'application/json\'].'
                );
            }
        }
    }
}
