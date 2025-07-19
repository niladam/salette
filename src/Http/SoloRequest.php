<?php

declare(strict_types=1);

namespace Salette\Http;

use Salette\Connectors\NullConnector;
use Salette\Requests\Request;
use Salette\Traits\Request\HasConnector;

abstract class SoloRequest extends Request
{
    use HasConnector;

    /**
     * Create a new connector instance.
     */
    protected function resolveConnector(): Connector
    {
        return new NullConnector();
    }
}
