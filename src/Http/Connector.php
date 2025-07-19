<?php

declare(strict_types=1);

namespace Salette\Http;

use Salette\Traits\Auth\AuthenticatesRequests;
use Salette\Traits\Bootable;
use Salette\Traits\Conditionable;
use Salette\Traits\Connector\SendsRequests;
use Salette\Traits\HandlesPsrRequest;
use Salette\Traits\HasDebugging;
use Salette\Traits\HasMockClient;
use Salette\Traits\Makeable;
use Salette\Traits\ManagesExceptions;
use Salette\Traits\Request\CreatesDtoFromResponse;
use Salette\Traits\RequestProperties\HasRequestProperties;
use Salette\Traits\RequestProperties\HasTries;
use Salette\Traits\Responses\HasCustomResponses;

abstract class Connector
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
    use SendsRequests;

    /**
     * Define the base URL of the API.
     */
    abstract public function resolveBaseUrl(): string;
}
