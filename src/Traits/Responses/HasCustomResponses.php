<?php

declare(strict_types=1);

namespace Salette\Traits\Responses;

/**
 * @phpstan-ignore trait.unused
 */
trait HasCustomResponses
{
    /**
     * Specify a default response.
     *
     * When null or an empty string, the response on the sender will be used.
     */
    protected ?string $response = null;

    /**
     * Resolve the custom response class
     */
    public function resolveResponseClass(): ?string
    {
        return $this->response ?? null;
    }
}
