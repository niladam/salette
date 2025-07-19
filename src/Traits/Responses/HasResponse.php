<?php

declare(strict_types=1);

namespace Salette\Traits\Responses;

use Salette\Http\Response;

/**
 * @phpstan-ignore trait.unused
 */
trait HasResponse
{
    /**
     * The original response.
     */
    protected Response $response;

    /**
     * Set the response on the data object.
     */
    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get the response on the data object.
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}
