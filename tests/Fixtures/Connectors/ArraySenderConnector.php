<?php

declare(strict_types=1);
namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Connector;
use Salette\Traits\Plugins\AcceptsJson;
use Salette\Tests\Fixtures\Senders\ArraySender;
use Salette\Contracts\Sender;

class ArraySenderConnector extends Connector
{
    use AcceptsJson;

    /**
     * Define the default sender class
     */
    protected string $defaultSender = ArraySender::class;

    /**
     * Define the base url of the api.
     */
    public function resolveBaseUrl(): string
    {
        return apiUrl();
    }

    /**
     * Define the default request sender.
     */
    protected function defaultSender(): Sender
    {
        return new $this->defaultSender();
    }

    /**
     * Set the sender
     *
     * @return $this
     */
    public function setDefaultSender(string $defaultSender)
    {
        $this->defaultSender = $defaultSender;

        return $this;
    }
}
