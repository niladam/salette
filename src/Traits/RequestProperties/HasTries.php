<?php

declare(strict_types=1);

namespace Salette\Traits\RequestProperties;

use Salette\Exceptions\FatalRequestException;
use Salette\Exceptions\RequestException;
use Salette\Requests\Request;

trait HasTries
{
    /**
     * The number of times a request should be retried if a failure response is returned.
     *
     * Set to null to disable the retry functionality.
     */
    public ?int $tries = null;

    /**
     * The interval in milliseconds Salette should wait between retries.
     *
     * For example 500ms = 0.5 seconds.
     *
     * Set to null to disable the retry interval.
     */
    public ?int $retryInterval = null;

    /**
     * Should Salette use exponential backoff during retries?
     *
     * When true, Salette will double the retry interval after each attempt.
     */
    public ?bool $useExponentialBackoff = null;

    /**
     * Should Salette throw an exception after exhausting the maximum number of retries?
     *
     * When false, Salette will return the last response attempted.
     *
     * Set to null to always throw after maximum retry attempts.
     */
    public ?bool $throwOnMaxTries = null;

    /**
     * Define whether the request should be retried.
     *
     * You can access the response from the RequestException. You can also modify the
     * request before the next attempt is made.
     */
    /**
     * @param FatalRequestException|RequestException $exception
     */
    public function handleRetry($exception, Request $request): bool
    {
        return true;
    }
}
