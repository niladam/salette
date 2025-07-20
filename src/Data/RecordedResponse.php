<?php

declare(strict_types=1);

namespace Salette\Data;

use JsonSerializable;
use Salette\Http\Faking\MockResponse;
use Salette\Http\Response;

class RecordedResponse implements JsonSerializable
{
    public int $statusCode;

    public array $headers;

    /**
     * @var mixed
     */
    public $data;

    public array $context;

    /**
     * Constructor
     */
    public function __construct($statusCode, array $headers = [], $data = null, array $context = [])
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->data = $data;
        $this->context = $context;
    }

    /**
     * Create an instance from file contents
     *
     * @throws \JsonException
     */
    public static function fromFile(string $contents): self
    {
        /**
         * @param array{
         *     statusCode: int,
         *     headers: array<string, mixed>,
         *     data: mixed,
         *     context: array<string, mixed>,
         * } $fileData
         */
        $fileData = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

        $data = $fileData['data'];

        if (isset($fileData['encoding']) && $fileData['encoding'] === 'base64') {
            $data = base64_decode($data);
        }

        return new static(
            $fileData['statusCode'],
            $fileData['headers'],
            $data,
            $fileData['context'] ?? [],
        );
    }

    /**
     * Create an instance from a Response
     */
    public static function fromResponse(Response $response): self
    {
        return new static(
            $response->status(),
            $response->headers()->all(),
            $response->body(),
        );
    }

    /**
     * Encode the instance to be stored as a file
     *
     * @throws \JsonException
     */
    public function toFile(): string
    {
        return json_encode($this, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    /**
     * Create a mock response from the fixture
     */
    public function toMockResponse(): MockResponse
    {
        return new MockResponse($this->data, $this->statusCode, $this->headers);
    }

    /**
     * Define the JSON object if this class is converted into JSON
     *
     * @return array{
     *     statusCode: int,
     *     headers: array<string, mixed>,
     *     data: mixed,
     * }
     */
    public function jsonSerialize(): array
    {
        $response = [
            'statusCode' => $this->statusCode,
            'headers' => $this->headers,
            'data' => $this->data,
            'context' => $this->context,
        ];

        if (mb_check_encoding($response['data'], 'UTF-8') === false) {
            $response['data'] = base64_encode($response['data']);
            $response['encoding'] = 'base64';
        }

        return $response;
    }
}
