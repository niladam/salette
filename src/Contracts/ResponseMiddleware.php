<?php

declare(strict_types=1);

namespace Salette\Contracts;

use Salette\Http\Response;

interface ResponseMiddleware
{
    /**
     * Register a response middleware
     *
     * @return Response|void
     */
    public function __invoke(Response $response);
}
