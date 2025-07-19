<?php

declare(strict_types=1);

namespace Salette\Senders;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Salette\Contracts\Sender;
use Salette\Data\FactoryCollection;
use Salette\Exceptions\FatalRequestException;
use Salette\Http\Response;
use Salette\Requests\PendingRequest;

class GuzzleSender implements Sender
{
    /**
     * The Guzzle client.
     */
    protected GuzzleClient $client;

    /**
     * Guzzle's Handler Stack.
     */
    protected HandlerStack $handlerStack;

    public function __construct()
    {
        $this->client = $this->createGuzzleClient();
    }

    /**
     * Get the factory collection.
     */
    public function getFactoryCollection(): FactoryCollection
    {
        $factory = new HttpFactory();

        return new FactoryCollection(
            $factory,
            $factory,
            $factory,
            $factory,
            new GuzzleMultipartBodyFactory()
        );
    }

    /**
     * Create a new Guzzle client.
     */
    protected function createGuzzleClient(): GuzzleClient
    {
        $this->handlerStack = HandlerStack::create();

        return new GuzzleClient([
            RequestOptions::CRYPTO_METHOD => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
            RequestOptions::CONNECT_TIMEOUT => 10,
            RequestOptions::TIMEOUT => 30,
            RequestOptions::HTTP_ERRORS => true,
            'handler' => $this->handlerStack,
        ]);
    }

    /**
     * Send a synchronous request.
     */
    public function send(PendingRequest $pendingRequest): Response
    {
        $request = $pendingRequest->createPsrRequest();

        $requestOptions = $pendingRequest->config()->all();

        try {
            $guzzleResponse = $this->client->send($request, $requestOptions);

            return $this->createResponse($guzzleResponse, $pendingRequest, $request);
        } catch (ConnectException $exception) {
            throw new FatalRequestException($exception, $pendingRequest);
        } catch (RequestException $exception) {
            $guzzleResponse = $exception->getResponse();
            if (is_null($guzzleResponse)) {
                throw new FatalRequestException($exception, $pendingRequest);
            }

            return $this->createResponse($guzzleResponse, $pendingRequest, $request, $exception);
        }
    }

    /**
     * Send an asynchronous request.
     */
    public function sendAsync(PendingRequest $pendingRequest): PromiseInterface
    {
        $request = $pendingRequest->createPsrRequest();
        $requestOptions = $pendingRequest->config()->all();

        $promise = $this->client->sendAsync($request, $requestOptions);

        return $this->processPromise($request, $promise, $pendingRequest);
    }

    /**
     * Update the promise provided by Guzzle.
     */
    protected function processPromise(
        RequestInterface $psrRequest,
        PromiseInterface $promise,
        PendingRequest $pendingRequest
    ): PromiseInterface {
        return $promise->then(
            function (ResponseInterface $guzzleResponse) use ($psrRequest, $pendingRequest) {
                return $this->createResponse($guzzleResponse, $pendingRequest, $psrRequest);
            },
            function (TransferException $guzzleException) use ($pendingRequest, $psrRequest) {
                if (! $guzzleException instanceof RequestException) {
                    throw new FatalRequestException($guzzleException, $pendingRequest);
                }

                $guzzleResponse = $guzzleException->getResponse();
                if (is_null($guzzleResponse)) {
                    throw new FatalRequestException($guzzleException, $pendingRequest);
                }

                $response = $this->createResponse($guzzleResponse, $pendingRequest, $psrRequest, $guzzleException);

                $exception = $response->toException();
                if ($exception) {
                    throw $exception;
                }

                return $response;
            }
        );
    }

    /**
     * Create a response.
     */
    protected function createResponse(
        ResponseInterface $psrResponse,
        PendingRequest $pendingRequest,
        RequestInterface $psrRequest,
        ?Exception $exception = null
    ): Response {
        /** @var class-string<Response> $responseClass */
        $responseClass = $pendingRequest->getResponseClass();

        return $responseClass::fromPsrResponse($psrResponse, $pendingRequest, $psrRequest, $exception);
    }

    /**
     * Add a middleware to the handler stack.
     *
     * @return $this
     */
    public function addMiddleware(callable $callable, string $name = ''): GuzzleSender
    {
        $this->handlerStack->push($callable, $name);

        return $this;
    }

    /**
     * Overwrite the entire handler stack.
     *
     * @return $this
     */
    public function setHandlerStack(HandlerStack $handlerStack): GuzzleSender
    {
        $this->handlerStack = $handlerStack;

        return $this;
    }

    /**
     * Get the handler stack.
     */
    public function getHandlerStack(): HandlerStack
    {
        return $this->handlerStack;
    }

    /**
     * Get the Guzzle client.
     */
    public function getGuzzleClient(): GuzzleClient
    {
        return $this->client;
    }
}
