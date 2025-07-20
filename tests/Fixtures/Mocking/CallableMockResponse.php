<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Mocking;

use Salette\Requests\PendingRequest;
use Salette\Http\Faking\MockResponse;

class CallableMockResponse
{
    public function __invoke(PendingRequest $pendingRequest): MockResponse
    {
        return new MockResponse(['request_class' => get_class($pendingRequest->getRequest())], 200);
    }
}
