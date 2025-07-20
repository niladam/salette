<?php

declare(strict_types=1);

namespace Salette\Requests;

use Salette\Enums\Method;
use Salette\Traits\Auth\AuthenticatesRequests;
use Salette\Traits\Bootable;
use Salette\Traits\Conditionable;
use Salette\Traits\HandlesPsrRequest;
use Salette\Traits\HasDebugging;
use Salette\Traits\HasMockClient;
use Salette\Traits\Makeable;
use Salette\Traits\ManagesExceptions;
use Salette\Traits\Request\CreatesDtoFromResponse;
use Salette\Traits\RequestProperties\HasRequestProperties;
use Salette\Traits\RequestProperties\HasTries;
use Salette\Traits\Responses\HasCustomResponses;

abstract class Request
{
    use AuthenticatesRequests;
    use Bootable;
    use Conditionable;
    use CreatesDtoFromResponse;
    use HandlesPsrRequest;
    use HasCustomResponses;
    use HasDebugging;
    use HasMockClient;
    use HasRequestProperties;
    use HasTries;
    use Makeable;
    use ManagesExceptions;

    public const METHOD = '';

    /**
     * Get the HTTP method for this request.
     */
    public function getMethod(): string
    {
        Method::validate(static::METHOD);

        return static::METHOD;
    }

    /**
     * Define the endpoint for the request.
     */
    abstract public function resolveEndpoint(): string;
}
