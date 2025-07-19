<?php

declare(strict_types=1);

namespace Salette\Contracts\DataObjects;

use Salette\Http\Response;

interface WithResponse
{
    /**
     * Set the response on the data object.
     *
     * @return $this
     */
    public function setResponse(Response $response): self;

    /**
     * Get the response on the data object.
     */
    public function getResponse(): Response;
}
