<?php

declare(strict_types=1);

namespace Salette\Traits\Request;

use GuzzleHttp\Promise\PromiseInterface;
use Salette\Contracts\Sender;
use Salette\Http\Connector;
use Salette\Http\Response;
use Salette\Requests\PendingRequest;

trait HasConnector
{
    /**
     * The loaded connector used in requests.
     */
    private ?Connector $loadedConnector = null;

    /**
     *  Retrieve the loaded connector.
     */
    public function connector(): Connector
    {
        return $this->loadedConnector ??= $this->resolveConnector();
    }

    /**
     * Set the loaded connector at runtime.
     */
    public function setConnector(Connector $connector): self
    {
        $this->loadedConnector = $connector;

        return $this;
    }

    /**
     * Create a new connector instance.
     */
    protected function resolveConnector(): Connector
    {
        return new $this->connector();
    }

    /**
     * Access the HTTP sender
     */
    public function sender(): Sender
    {
        return $this->connector()->sender();
    }

    /**
     * Create a pending request
     */
    public function createPendingRequest(): PendingRequest
    {
        return $this->connector()->createPendingRequest($this);
    }

    /**
     * Send a request synchronously
     */
    public function send(): Response
    {
        return $this->connector()->send($this);
    }

    /**
     * Send a request asynchronously
     */
    public function sendAsync(): PromiseInterface
    {
        return $this->connector()->sendAsync($this);
    }
}
