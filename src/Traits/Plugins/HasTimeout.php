<?php

declare(strict_types=1);

namespace Salette\Traits\Plugins;

use GuzzleHttp\RequestOptions;
use Salette\Requests\PendingRequest;

/**
 * @phpstan-ignore trait.unused
 */
trait HasTimeout
{
    /**
     * Boot HasTimeout plugin.
     */
    public function bootHasTimeout(PendingRequest $pendingRequest): void
    {
        $pendingRequest->config()->merge(
            [
                RequestOptions::CONNECT_TIMEOUT => $this->getConnectTimeout(),
                RequestOptions::TIMEOUT => $this->getRequestTimeout(),
            ]
        );
    }

    /**
     * Get the request connection timeout.
     */
    public function getConnectTimeout(): float
    {
        return property_exists($this, 'connectTimeout') ? $this->connectTimeout : 30;
    }

    /**
     * Get the request timeout.
     */
    public function getRequestTimeout(): float
    {
        return property_exists($this, 'requestTimeout') ? $this->requestTimeout : 10;
    }
}
