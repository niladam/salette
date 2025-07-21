<?php

declare(strict_types=1);

namespace Salette\Http;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use JsonException;
use LogicException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Salette\Contracts\ArrayStore as ArrayStoreContract;
use Salette\Contracts\DataObjects\WithResponse;
use Salette\Contracts\FakeResponse;
use Salette\Exceptions\SaletteException;
use Salette\Helpers\ArrayHelpers;
use Salette\Helpers\ObjectHelpers;
use Salette\Helpers\RequestExceptionHelper;
use Salette\Repositories\ArrayStore;
use Salette\Requests\PendingRequest;
use Salette\Requests\Request;
use Salette\Traits\Macroable;
use SimpleXMLElement;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class Response
{
    use Macroable;

    /**
     * The PSR request.
     */
    protected RequestInterface $psrRequest;

    /**
     * The PSR response from the sender.
     */
    protected ResponseInterface $psrResponse;

    /**
     * The pending request that has all the request properties.
     */
    protected PendingRequest $pendingRequest;

    /**
     * The original sender exception.
     */
    protected ?Throwable $senderException = null;

    /**
     * The decoded JSON response.
     *
     * @var array<array-key, mixed>
     */
    protected array $decodedJson;

    /**
     * The decoded JSON response object.
     *
     * @var mixed
     */
    protected $decodedJsonObject;

    /**
     * The decoded XML response.
     */
    protected string $decodedXml;

    /**
     * Denotes if the response has been mocked.
     */
    protected bool $mocked = false;

    /**
     * Denotes if the response has been cached.
     */
    protected bool $cached = false;

    /**
     * The simulated response payload if the response was simulated.
     */
    protected ?FakeResponse $fakeResponse = null;

    /**
     * Create a new response instance.
     */
    public function __construct(
        ResponseInterface $psrResponse,
        PendingRequest $pendingRequest,
        RequestInterface $psrRequest,
        ?Throwable $senderException = null
    ) {
        $this->psrRequest = $psrRequest;
        $this->psrResponse = $psrResponse;
        $this->pendingRequest = $pendingRequest;
        $this->senderException = $senderException;
    }

    /**
     * Create a new response instance from a PSR response.
     *
     * @return static
     */
    public static function fromPsrResponse(
        ResponseInterface $psrResponse,
        PendingRequest $pendingRequest,
        RequestInterface $psrRequest,
        ?Throwable $senderException = null
    ): Response {
        // @phpstan-ignore-next-line Unsafe usage of new static() is intentional
        return new static($psrResponse, $pendingRequest, $psrRequest, $senderException);
    }

    /**
     * Get the pending request that created the response.
     */
    public function getPendingRequest(): PendingRequest
    {
        return $this->pendingRequest;
    }

    /**
     * Get the connector that sent the request.
     */
    public function getConnector(): Connector
    {
        return $this->pendingRequest->getConnector();
    }

    /**
     * Get the original request that created the response.
     */
    public function getRequest(): Request
    {
        return $this->pendingRequest->getRequest();
    }

    /**
     * Get the PSR-7 request.
     */
    public function getPsrRequest(): RequestInterface
    {
        return $this->psrRequest;
    }

    /**
     * Create a PSR response from the raw response.
     */
    public function getPsrResponse(): ResponseInterface
    {
        return $this->psrResponse;
    }

    /**
     * Get the body of the response as a string.
     */
    public function body(): string
    {
        $stream = $this->stream();
        $contents = $stream->getContents();
        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        return $contents;
    }

    /**
     * Get the body as a stream.
     */
    public function stream(): StreamInterface
    {
        $stream = $this->psrResponse->getBody();
        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        return $stream;
    }

    /**
     * Get the headers from the response.
     *
     * @return ArrayStoreContract
     */
    public function headers()
    {
        $headers = array_map(
            function (array $header) {
                return count($header) === 1 ? $header[0] : $header;
            }, $this->psrResponse->getHeaders()
        );

        return new ArrayStore($headers);
    }

    /**
     * Get the status code of the response.
     */
    public function status(): int
    {
        return $this->psrResponse->getStatusCode();
    }

    /**
     * Get the original sender exception.
     */
    public function getSenderException(): ?Throwable
    {
        return $this->senderException;
    }

    /**
     * Get the JSON decoded body of the response as an array or scalar value.
     *
     * @param  string|int|null $key
     * @param  mixed           $default
     * @return mixed
     *
     * @throws \JsonException
     */
    public function json($key = null, $default = null)
    {
        if (! isset($this->decodedJson)) {
            $this->decodedJson = json_decode($this->body() ?: '[]', true, 512, JSON_THROW_ON_ERROR);
        }

        if ($key === null) {
            return $this->decodedJson;
        }

        return ArrayHelpers::get($this->decodedJson, $key, $default);
    }

    /**
     * Alias of json().
     *
     * @param  string|int|null $key
     * @param  mixed           $default
     * @return mixed
     *
     * @throws \JsonException
     */
    public function array($key = null, $default = null)
    {
        return $this->json($key, $default);
    }

    /**
     * Get the JSON decoded body of the response as an object or scalar value.
     *
     * @param  string|int|null $key
     * @param  mixed           $default
     * @return mixed
     *
     * @throws \JsonException
     */
    public function object($key = null, $default = null)
    {
        if (! isset($this->decodedJsonObject)) {
            $this->decodedJsonObject = json_decode($this->body() ?: '{}', false, 512, JSON_THROW_ON_ERROR);
        }

        if (is_null($key)) {
            return $this->decodedJsonObject;
        }

        return ObjectHelpers::get($this->decodedJsonObject, (string) $key, $default);
    }

    /**
     * Convert the XML response into a SimpleXMLElement.
     *
     * Suitable for reading small, simple XML responses but not suitable for
     * more advanced XML responses with namespaces and prefixes. Consider
     * using the xmlReader method instead for better compatibility.
     *
     * @see https://www.php.net/manual/en/book.simplexml.php
     *
     * @phpstan-ignore-next-line
     *
     * @return SimpleXMLElement|false
     */
    public function xml(...$arguments)
    {
        if (! isset($this->decodedXml)) {
            $this->decodedXml = $this->body();
        }

        return simplexml_load_string($this->decodedXml, ...$arguments);
    }

    /**
     * Load the XML response into a reader
     *
     * Suitable for reading XML responses using dot notation to access elements.
     * Supports array-like access for multiple elements of the same type.
     *
     * @throws SaletteException
     */
    public function xmlReader()
    {
        if (! class_exists('SimpleXMLElement')) {
            throw new SaletteException(
                'You are missing the SimpleXMLElement class.'
                . 'This is a core PHP extension that should be available.'
            );
        }

        return new XmlReader($this->body());
    }

    /**
     * Get the JSON decoded body of the response as a collection.
     *
     * @param string|int|null $key
     *
     * @throws JsonException|SaletteException
     *
     * @phpstan-ignore-next-line
     */
    public function collect($key = null): Collection
    {
        if (! class_exists('Illuminate\Support\Collection')) {
            throw new SaletteException(
                'You are missing the Illuminate\Support\Collection class.'
                . 'You can install it by running composer require illuminate/collections.'
            );
        }

        $data = $this->json($key);

        if ($data === null) {
            return Collection::empty();
        }

        if (is_array($data)) {
            return Collection::make($data);
        }

        return Collection::make([$data]);
    }

    /**
     * Cast the response to a DTO.
     *
     * @return mixed
     */
    public function dto()
    {
        $request = $this->pendingRequest->getRequest();
        $connector = $this->pendingRequest->getConnector();

        $dataObject = $request->createDtoFromResponse($this) ?? $connector->createDtoFromResponse($this);

        if ($dataObject instanceof WithResponse) {
            $dataObject->setResponse($this);
        }

        return $dataObject;
    }

    /**
     * Convert the response into a DTO or throw a LogicException if the response failed.
     *
     * @return mixed
     *
     * @throws LogicException
     */
    public function dtoOrFail()
    {
        if ($this->failed()) {
            throw new LogicException(
                'Unable to create data transfer object as the response has failed.',
                0,
                $this->toException()
            );
        }

        return $this->dto();
    }

    /**
     * Parse the HTML or XML body into a Symfony DomCrawler instance.
     *
     * Requires Symfony Crawler (composer require symfony/dom-crawler)
     *
     * @see https://symfony.com/doc/current/components/dom_crawler.html
     *
     * @throws SaletteException
     */
    public function dom()
    {
        if (! class_exists('Symfony\Component\DomCrawler\Crawler')) {
            throw new SaletteException(
                'You are missing the Symfony\Component\DomCrawler\Crawler class.'
                . 'You can install it by running composer require symfony/dom-crawler.'
            );
        }

        return new Crawler($this->body());
    }

    /**
     * Convert the response to a data URL.
     */
    public function dataUrl(): string
    {
        return 'data:' . $this->psrResponse->getHeaderLine('Content-Type')
               . ';base64,' . base64_encode($this->body());
    }

    /**
     * Determine if the request was successful.
     */
    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    /**
     * Determine if the response code was "OK".
     */
    public function ok(): bool
    {
        return $this->status() === 200;
    }

    /**
     * Determine if the response was "NO CONTENT"
     */
    public function noContent(): bool
    {
        return $this->status() === 204;
    }

    /**
     * Determine if the response was "DELETED" -- as per API recommendations.
     */
    public function deleted(): bool
    {
        return $this->noContent();
    }

    /**
     * Determine if the response was a redirect.
     */
    public function redirect(): bool
    {
        return $this->status() >= 300 && $this->status() < 400;
    }

    /**
     * Determine if the response indicates a client or server error occurred.
     */
    public function failed(): bool
    {
        $pendingRequest = $this->getPendingRequest();

        $requestFailedAccordingToConnector =
            $pendingRequest->getConnector()->hasRequestFailed($this);
        $requestFailedAccordingToRequest =
            $pendingRequest->getRequest()->hasRequestFailed($this);

        if ($requestFailedAccordingToRequest !== null
            || $requestFailedAccordingToConnector !== null
        ) {
            return $requestFailedAccordingToRequest
                   || $requestFailedAccordingToConnector;
        }

        return $this->serverError() || $this->clientError();
    }

    /**
     * Determine if the response indicates a client error occurred.
     */
    public function clientError(): bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    /**
     * Determine if the response indicates a server error occurred.
     */
    public function serverError(): bool
    {
        return $this->status() >= 500;
    }

    /**
     * Execute the given callback if there was a server or client error.
     *
     * @return $this
     */
    public function onError(callable $callback): Response
    {
        if ($this->failed()) {
            $callback($this);
        }

        return $this;
    }

    /**
     * Determine if the response should throw a request exception.
     */
    public function shouldThrowRequestException(): bool
    {
        $pendingRequest = $this->getPendingRequest();

        return $pendingRequest->getRequest()->shouldThrowRequestException($this)
               || $pendingRequest->getConnector()->shouldThrowRequestException($this);
    }

    /**
     * Create an exception if a server or client error occurred.
     */
    public function toException(): ?Throwable
    {
        if (! $this->shouldThrowRequestException()) {
            return null;
        }

        return $this->createException();
    }

    /**
     * Create the request exception.
     */
    protected function createException(): Throwable
    {
        $pendingRequest = $this->getPendingRequest();
        $senderException = $this->getSenderException();

        $exception = $pendingRequest->getRequest()->getRequestException(
            $this,
            $senderException
        ) ?? $pendingRequest->getConnector()->getRequestException(
            $this,
            $senderException
        );

        if ($exception instanceof Throwable) {
            return $exception;
        }

        return RequestExceptionHelper::create($this, $senderException);
    }

    /**
     * Throw an exception if a server or client error occurred.
     *
     * @return $this
     *
     * @throws Throwable
     */
    public function throw(): Response
    {
        if ($this->shouldThrowRequestException()) {
            throw $this->toException();
        }

        return $this;
    }

    /**
     * Get a header from the response.
     *
     * @return string|array<array-key,mixed>|null
     */
    public function header(string $header)
    {
        return $this->headers()->get($header);
    }

    /**
     * Determine if the response is in JSON format.
     */
    public function isJson(): bool
    {
        $contentType = $this->header('Content-Type');

        if (is_null($contentType)) {
            return false;
        }

        $contentType = is_array($contentType) ? $contentType[0] : $contentType;

        return strpos($contentType, 'json') !== false;
    }

    /**
     * Determine if the response is in XML format.
     */
    public function isXml(): bool
    {
        $contentType = $this->header('Content-Type');

        if (is_null($contentType)) {
            return false;
        }

        $contentType = is_array($contentType) ? $contentType[0] : $contentType;

        return strpos($contentType, 'xml') !== false;
    }

    /**
     * Create a temporary resource for the stream.
     *
     * @return resource
     *
     * @throws LogicException
     */
    public function getRawStream()
    {
        $temporaryResource = fopen('php://temp', 'wb+');

        if ($temporaryResource === false) {
            throw new LogicException('Unable to create a temporary resource for the stream.');
        }

        $this->saveBodyToFile($temporaryResource, false);

        return $temporaryResource;
    }

    /**
     * Save the body to a file.
     *
     * @param string|resource $resourceOrPath
     */
    public function saveBodyToFile($resourceOrPath, bool $closeResource = true): void
    {
        if (! is_string($resourceOrPath) && ! is_resource($resourceOrPath)) {
            throw new InvalidArgumentException(
                "The $resourceOrPath argument must be either a file path or a resource."
            );
        }

        $resource = is_string($resourceOrPath)
            ? fopen($resourceOrPath, 'wb+')
            : $resourceOrPath;

        if ($resource === false) {
            throw new LogicException('Unable to open the resource.');
        }

        rewind($resource);

        $stream = $this->stream();

        while (! $stream->eof()) {
            fwrite($resource, $stream->read(1024));
        }

        rewind($resource);

        if ($closeResource) {
            fclose($resource);
        }
    }

    /**
     * Close the stream and any underlying resources.
     *
     * @return $this
     */
    public function close(): self
    {
        $this->stream()->close();

        return $this;
    }

    /**
     * Get the body of the response.
     */
    public function __toString(): string
    {
        return $this->body();
    }

    /**
     * Check if the response has been cached.
     */
    public function isCached(): bool
    {
        return $this->cached;
    }

    /**
     * Check if the response has been mocked
     */
    public function isMocked(): bool
    {
        return $this->mocked;
    }

    /**
     * Check if the response has been simulated
     */
    public function isFaked(): bool
    {
        return $this->isMocked() || $this->isCached();
    }

    /**
     * Set if a response has been cached or not.
     *
     * @param  bool $value
     * @return $this
     */
    public function setCached($value): self
    {
        $this->cached = true;

        return $this;
    }

    /**
     * Set if a response has been mocked or not.
     *
     * @return $this
     */
    public function setMocked(bool $value): self
    {
        $this->mocked = true;

        return $this;
    }

    /**
     * Set the simulated response payload if the response was simulated.
     *
     * @return $this
     */
    public function setFakeResponse(FakeResponse $fakeResponse): self
    {
        $this->fakeResponse = $fakeResponse;

        return $this;
    }

    /**
     * Get the simulated response payload if the response was simulated.
     */
    public function getFakeResponse(): ?FakeResponse
    {
        return $this->fakeResponse;
    }
}
