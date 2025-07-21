<?php

declare(strict_types=1);

namespace Salette\Http\Faking;

use Closure;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Salette\Contracts\ArrayStore as ArrayStoreContract;
use Salette\Contracts\BodyRepository;
use Salette\Contracts\FakeResponse as FakeResponseContract;
use Salette\Exceptions\DirectoryNotFoundException;
use Salette\Exceptions\UnableToCreateDirectoryException;
use Salette\Repositories\ArrayStore;
use Salette\Repositories\JsonBodyRepository;
use Salette\Repositories\StringBodyRepository;
use Salette\Requests\PendingRequest;
use Salette\Traits\Makeable;
use Throwable;

class FakeResponse implements FakeResponseContract
{
    use Makeable;

    /**
     * HTTP Status Code
     */
    protected int $status;

    /**
     * Headers
     */
    protected ArrayStoreContract $headers;

    /**
     * Request Body
     */
    protected BodyRepository $body;

    /**
     * Exception Closure
     */
    protected ?Closure $responseException = null;

    /**
     * Create a new mock response
     *
     * @param array<string, mixed>|string $body
     * @param array<string, mixed>        $headers
     */
    public function __construct($body = [], int $status = 200, array $headers = [])
    {
        $this->body = is_array($body) ? new JsonBodyRepository($body) : new StringBodyRepository($body);
        $this->status = $status;
        $this->headers = new ArrayStore($headers);
    }

    /**
     *  Get the response body
     */
    public function body(): BodyRepository
    {
        return $this->body;
    }

    /**
     * Get the status from the responses
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * Get the headers
     */
    public function headers(): ArrayStoreContract
    {
        return $this->headers;
    }

    /**
     * Throw an exception on the request.
     *
     * @return $this
     */
    public function throw($value): self
    {
        $closure = $value instanceof Throwable ? static fn () => $value : $value;

        $this->responseException = $closure;

        return $this;
    }

    /**
     * Invoke the exception.
     */
    public function getException(PendingRequest $pendingRequest): ?Throwable
    {
        if (! $this->responseException instanceof Closure) {
            return null;
        }

        return call_user_func($this->responseException, $pendingRequest);
    }

    /**
     * Create a new mock response from a fixture
     *
     * @throws DirectoryNotFoundException
     * @throws UnableToCreateDirectoryException
     */
    public static function fixture(string $name): Fixture
    {
        return new Fixture($name);
    }

    /**
     * Get the response as a ResponseInterface
     */
    public function createPsrResponse(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory
    ): ResponseInterface {
        $response = $responseFactory->createResponse($this->status());

        foreach ($this->headers()->all() as $headerName => $headerValue) {
            $response = $response->withHeader($headerName, $headerValue);
        }

        return $response->withBody($this->body()->toStream($streamFactory));
    }
}
