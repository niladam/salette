<?php

declare(strict_types=1);
namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Connector;
use Salette\Traits\Plugins\AcceptsJson;
use Salette\Tests\Fixtures\Senders\ArraySender;

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
