<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Plugins;

use Salette\Requests\Request;
use Salette\Http\Connector;
use Salette\Requests\PendingRequest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait HasTestHandler
{
    /**
     * Boot a test handler that adds a simple header to the response.
     *
     * @return void
     */
    public function bootHasTestHandler(PendingRequest $pendingRequest)
    {
        $connector = $pendingRequest->getConnector();

        $connector->sender()->addMiddleware(function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $promise = $handler($request, $options);

                return $promise->then(
                    function (ResponseInterface $response) {
                        $response = $response->withHeader('X-Test-Handler', true);

                        if ($this instanceof Connector) {
                            $response = $response->withHeader('X-Handler-Added-To', 'connector');
                        }

                        if ($this instanceof Request) {
                            $response = $response->withHeader('X-Handler-Added-To', 'request');
                        }

                        return $response;
                    }
                );
            };
        }, 'testHandler');
    }
}
