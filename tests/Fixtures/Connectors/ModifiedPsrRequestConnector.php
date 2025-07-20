<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use GuzzleHttp\Psr7\Uri;
use Salette\Requests\PendingRequest;
use Psr\Http\Message\RequestInterface;

class ModifiedPsrRequestConnector extends TestConnector
{
    public function handlePsrRequest(RequestInterface $request, PendingRequest $pendingRequest): RequestInterface
    {
        return $request->withUri(new Uri('https://google.com'));
    }
}
