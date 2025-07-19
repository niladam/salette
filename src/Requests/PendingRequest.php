<?php

declare(strict_types=1);

namespace Salette\Requests;

use ReflectionException;
use Salette\Config;
use Salette\Contracts\Authenticator;
use Salette\Contracts\BodyRepository;
use Salette\Contracts\FakeResponse;
use Salette\Enums\Method;
use Salette\Exceptions\DuplicatePipeNameException;
use Salette\Exceptions\FatalRequestException;
use Salette\Exceptions\InvalidHttpMethod;
use Salette\Exceptions\InvalidResponseClassException;
use Salette\Helpers\BootPlugins;
use Salette\Helpers\URLHelper;
use Salette\Http\AuthenticatePendingRequest;
use Salette\Http\BootConnectorAndRequest;
use Salette\Http\Connector;
use Salette\Http\Faking\MockClient;
use Salette\Http\MergeBody;
use Salette\Http\MergeDelay;
use Salette\Http\MergeRequestProperties;
use Salette\Http\Middleware\DelayMiddleware;
use Salette\Http\Middleware\DetermineMockResponse;
use Salette\Http\Middleware\ValidateProperties;
use Salette\Http\Response;
use Salette\Support\Helpers;
use Salette\Traits\Auth\AuthenticatesRequests;
use Salette\Traits\Conditionable;
use Salette\Traits\HasMockClient;
use Salette\Traits\Macroable;
use Salette\Traits\PendingRequest\ManagesPsrRequests;
use Salette\Traits\RequestProperties\HasRequestProperties;

class PendingRequest
{
    use AuthenticatesRequests;
    use Conditionable;
    use HasMockClient;
    use HasRequestProperties;
    use Macroable;
    use ManagesPsrRequests;

    /**
     * The connector making the request.
     */
    protected Connector $connector;

    /**
     * The request used by the instance.
     */
    protected Request $request;

    /**
     * The method the request will use.
     */
    public const METHOD = 'GET';

    /**
     * The URL the request will be made to.
     */
    protected string $url;

    /**
     * The body of the request.
     */
    protected ?BodyRepository $body = null;

    /**
     * The simulated response.
     */
    protected ?FakeResponse $fakeResponse = null;

    /**
     * Determine if the pending request is asynchronous
     */
    protected bool $asynchronous = false;

    protected string $method = Method::GET;

    /**
     * Build up the request payload.
     *
     * @throws DuplicatePipeNameException
     * @throws \Exception
     */
    public function __construct(Connector $connector, Request $request, ?MockClient $mockClient = null)
    {
        // Let's start by getting our PSR factory collection. This object contains all the
        // relevant factories for creating PSR-7 requests as well as URIs and streams.

        $this->factoryCollection = $connector->sender()->getFactoryCollection();

        // Now we'll set the base properties

        $this->connector = $connector;
        $this->request = $request;
        $this->method = $request->getMethod();
        $this->url = URLHelper::join($this->connector->resolveBaseUrl(), $this->request->resolveEndpoint());
        $this->authenticator = $request->getAuthenticator() ?? $connector->getAuthenticator();

        $this->mockClient = $mockClient
            ?? $request->getMockClient()
            ?? $connector->getMockClient()
            ?? MockClient::getGlobal();

        // Now, we'll register our global middleware and our mock response middleware.
        // Registering these middleware first means that the mock client can set
        // the fake response for every subsequent middleware.

        $this->middleware()->merge(Config::globalMiddleware());
        $this->middleware()->onRequest(new DetermineMockResponse(), 'determineMockResponse');

        // Next, we'll boot our plugins. These plugins can add headers, config variables and
        // even register their own middleware. We'll use a tap method to simply apply logic
        // to the PendingRequest. After that, we will merge together our request properties
        // like headers, config, middleware, body and delay, and we'll follow it up by
        // invoking our authenticators. We'll do this here because when middleware is
        // executed, the developer will have access to any headers added by the middleware.

        $this
            ->tap(new BootPlugins())
            ->tap(new MergeRequestProperties())
            ->tap(new MergeBody())
            ->tap(new MergeDelay())
            ->tap(new AuthenticatePendingRequest())
            ->tap(new BootConnectorAndRequest());

        // Now, we'll register some default middleware for validating the request properties and
        // running the delay that should have been set by the user.

        $this->middleware()
            ->onRequest(new ValidateProperties(), 'validateProperties')
            ->onRequest(new DelayMiddleware(), 'delayMiddleware');

        // Finally, we will execute the request middleware pipeline which will
        // process the middleware in the order we added it.

        $this->middleware()->executeRequestPipeline($this);
    }

    /**
     * Authenticate the PendingRequest
     */
    public function authenticate(Authenticator $authenticator): self
    {
        $this->authenticator = $authenticator;

        // Since the PendingRequest has already been constructed we will run the set
        // method on the authenticator which runs it straight away.

        $this->authenticator->set($this);

        return $this;
    }

    /**
     * Execute the response pipeline.
     */
    public function executeResponsePipeline(Response $response): Response
    {
        return $this->middleware()->executeResponsePipeline($response);
    }

    /**
     * Execute the fatal pipeline.
     */
    public function executeFatalPipeline(FatalRequestException $throwable): void
    {
        $this->middleware()->executeFatalPipeline($throwable);
    }

    /**
     * Get the request.
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Get the connector.
     */
    public function getConnector(): Connector
    {
        return $this->connector;
    }

    /**
     * Get the URL of the request.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set the URL of the PendingRequest
     *
     * Note: This will be combined with the query parameters to create
     * a UriInterface that will be passed to a PSR-7 request.
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the HTTP method used for the request
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * Set the method of the PendingRequest
     *
     * @throws InvalidHttpMethod
     */
    public function setMethod(string $method): self
    {
        Method::validate($method);

        $this->method = $method;

        return $this;
    }

    /**
     * Retrieve the body on the instance
     */
    public function body(): ?BodyRepository
    {
        return $this->body;
    }

    /**
     * Set the body repository
     */
    public function setBody(?BodyRepository $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the fake response
     */
    public function getFakeResponse(): ?FakeResponse
    {
        return $this->fakeResponse;
    }

    /**
     * Set the fake response
     */
    public function setFakeResponse(?FakeResponse $fakeResponse): self
    {
        $this->fakeResponse = $fakeResponse;

        return $this;
    }

    /**
     * Check if a fake response has been set
     */
    public function hasFakeResponse(): bool
    {
        return $this->fakeResponse instanceof FakeResponse;
    }

    /**
     * Check if the request is asynchronous
     */
    public function isAsynchronous(): bool
    {
        return $this->asynchronous;
    }

    /**
     * Set if the request is going to be sent asynchronously
     */
    public function setAsynchronous(bool $asynchronous): self
    {
        $this->asynchronous = $asynchronous;

        return $this;
    }

    /**
     * Get the response class
     *
     * @throws InvalidResponseClassException|ReflectionException
     */
    public function getResponseClass(): string
    {
        $response = $this->request->resolveResponseClass()
            ?? $this->connector->resolveResponseClass() ?? Response::class;

        if (! class_exists($response) || ! Helpers::isSubclassOf($response, Response::class)) {
            throw new InvalidResponseClassException();
        }

        return $response;
    }

    /**
     * Tap into the pending request
     */
    protected function tap(callable $callable): self
    {
        $callable($this);

        return $this;
    }
}
