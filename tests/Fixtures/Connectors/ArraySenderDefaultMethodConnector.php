<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Connector;
use Salette\Contracts\Sender;
use Salette\Traits\Plugins\AcceptsJson;
use Salette\Tests\Fixtures\Senders\ArraySender;

class ArraySenderDefaultMethodConnector extends Connector
{
    use AcceptsJson;

    /**
     * Define the base url of the api.
     */
    public function resolveBaseUrl(): string
    {
        return apiUrl();
    }

    /**
     * Default Sender
     */
    protected function defaultSender(): ArraySender
    {
        return new ArraySender;
    }
}
