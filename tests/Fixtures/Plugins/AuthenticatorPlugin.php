<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Plugins;

use Salette\Requests\PendingRequest;

trait AuthenticatorPlugin
{
    public function bootAuthenticatorPlugin(PendingRequest $pendingRequest)
    {
        $pendingRequest->withTokenAuth('plugin-auth');
    }
}
