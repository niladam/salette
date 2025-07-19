<?php

declare(strict_types=1);

namespace Salette\Contracts;

use GuzzleHttp\Promise\PromiseInterface;
use Salette\Data\FactoryCollection;
use Salette\Http\Response;
use Salette\Requests\PendingRequest;

interface Sender
{
    /**
     * Get the factory collection
     */
    public function getFactoryCollection(): FactoryCollection;

    /**
     * Send the request synchronously
     */
    public function send(PendingRequest $pendingRequest): Response;

    /**
     * Send the request asynchronously
     */
    public function sendAsync(PendingRequest $pendingRequest): PromiseInterface;
}
