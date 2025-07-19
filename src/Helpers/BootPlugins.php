<?php

declare(strict_types=1);

namespace Salette\Helpers;

use Salette\Requests\PendingRequest;
use Salette\Support\Helpers;

class BootPlugins
{
    /**
     * Boot the plugins
     */
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $connector = $pendingRequest->getConnector();
        $request = $pendingRequest->getRequest();

        $connectorTraits = Helpers::classUsesRecursive($connector);
        $requestTraits = Helpers::classUsesRecursive($request);

        foreach ($connectorTraits as $connectorTrait) {
            Helpers::bootPlugin($pendingRequest, $connector, $connectorTrait);
        }

        foreach ($requestTraits as $requestTrait) {
            Helpers::bootPlugin($pendingRequest, $request, $requestTrait);
        }

        return $pendingRequest;
    }
}
