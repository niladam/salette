<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Debuggers;

use Spatie\Ray\Client;
use Spatie\Ray\Request;

class FakeRay extends Client
{
    protected array $sentRequests = [];

    public function serverIsAvailable()
    {
        return true;
    }

    public function send(Request $request)
    {
        $requestProperties = $request->toArray();

        $this->sentRequests[] = $requestProperties;
    }

    public function getSentRequests()
    {
        return $this->sentRequests;
    }
}
