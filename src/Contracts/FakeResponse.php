<?php

declare(strict_types=1);

namespace Salette\Contracts;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Salette\Http\Faking\Fixture;
use Salette\Requests\PendingRequest;
use Throwable;

interface FakeResponse
{
    /**
     * Get the status from the responses
     */
    public function status(): int;

    /**
     * Get the headers
     */
    public function headers(): ArrayStore;

    /**
     * Get the response body
     */
    public function body(): BodyRepository;

    /**
     * Throw an exception on the request.
     *
     * @return $this
     */
    public function throw(Throwable $value): self;

    /**
     * Get the exception
     */
    public function getException(PendingRequest $pendingRequest): ?Throwable;

    /**
     * Create a new mock response from a fixture
     */
    public static function fixture(string $name): Fixture;

    /**
     * Get the response as a ResponseInterface
     */
    public function createPsrResponse(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory
    ): ResponseInterface;
}
