<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Requests\Request as RequestContract;

class RetryConnector extends TestConnector
{
    public function __construct(
        $tries = null,
        int $retryInterval = 0,
        $throwOnMaxTries = null,
        $handleRetry = null
    ) {
        // These are just for us to test the various retries

        $this->tries = $tries;
        $this->retryInterval = $retryInterval;
        $this->throwOnMaxTries = $throwOnMaxTries;
    }

    public function handleRetry($exception, RequestContract $request): bool
    {
        return isset($this->handleRetry) ? call_user_func($this->handleRetry, $exception, $request) : true;
    }
}
