<?php

declare(strict_types=1);

namespace Salette\Http;

class BaseResource
{
    protected Connector $connector;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }
}
