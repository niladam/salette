<?php

declare(strict_types=1);

namespace Salette\Traits;

use Salette\Enums\PipeOrder;
use Salette\Helpers\Debugger;
use Salette\Http\Response;
use Salette\Requests\PendingRequest;

trait HasDebugging
{
    /**
     * Register a request debugger
     *
     * Leave blank for a default debugger (requires symfony/var-dump)
     *
     * @return $this
     */
    public function debugRequest(?callable $onRequest = null, $die = false): self
    {
        if ($onRequest === null) {
            $onRequest = \Closure::fromCallable([Debugger::class, 'symfonyRequestDebugger']);
        }

        $this->middleware()->onRequest(
            static function (PendingRequest $pendingRequest) use ($onRequest, $die) {
                $onRequest($pendingRequest, $pendingRequest->createPsrRequest());

                if ($die) {
                    Debugger::die();
                }
            },
            PipeOrder::LAST
        );

        return $this;
    }

    /**
     * Register a response debugger
     *
     * Leave blank for a default debugger (requires symfony/var-dump)
     *
     * @return $this
     */
    public function debugResponse(?callable $onResponse = null, $die = false): self
    {
        if ($onResponse === null) {
            $onResponse = \Closure::fromCallable([Debugger::class, 'symfonyResponseDebugger']);
        }

        $this->middleware()->onResponse(
            static function (Response $response) use ($onResponse, $die) {
                $onResponse($response, $response->getPsrResponse());

                if ($die) {
                    Debugger::die();
                }
            },
            PipeOrder::FIRST
        );

        return $this;
    }

    /**
     * Dump a pretty output of the request and response.
     *
     * This is useful if you would like to see the request right before it is sent
     * to inspect the body and URI to ensure it is correct. You can also inspect
     * the raw response as it comes back.
     *
     * Note that any changes made to the PSR request by the sender will not be
     * reflected by this output.
     *
     * Requires symfony/var-dumper
     */
    public function debug(bool $die = false): self
    {
        return $this->debugRequest()->debugResponse(null, $die);
    }
}
