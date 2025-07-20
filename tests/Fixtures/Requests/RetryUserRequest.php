<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Closure;
use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Requests\Request as RequestContract;
use Salette\Exceptions\RequestException;
use Salette\Exceptions\FatalRequestException;

class RetryUserRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    public const METHOD = Method::GET;
    protected $handleRetry = null;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }

    public function __construct(
        $tries = null,
        int $retryInterval = 0,
        $throwOnMaxTries = null,
        $handleRetry = null
    ) {
        $this->handleRetry = $handleRetry;
        // These are just for us to test the various retries

        $this->tries = $tries;
        $this->retryInterval = $retryInterval;
        $this->throwOnMaxTries = $throwOnMaxTries;
    }

    public function handleRetry(
        $exception,
        RequestContract $request
    ): bool {
        return isset($this->handleRetry) ? call_user_func($this->handleRetry, $exception, $request) : true;
    }
}
