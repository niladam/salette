<?php

declare(strict_types=1);

namespace Salette\Traits\Request;

use Salette\Http\Response;

trait CreatesDtoFromResponse
{
    /**
     * Cast the response to a DTO.
     *
     * @return mixed
     */
    public function createDtoFromResponse(Response $response)
    {
        return null;
    }
}
