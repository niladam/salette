<?php

declare(strict_types=1);

namespace Salette\Helpers;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Salette\Http\Response;
use Salette\Requests\PendingRequest;
use Symfony\Component\VarDumper\VarDumper;

class Debugger
{
    /**
     * Application "Die" handler.
     *
     * Only used for Salette tests
     */
    public static ?Closure $dieHandler = null;

    /**
     * Debug a request with Symfony Var Dumper
     */
    public static function symfonyRequestDebugger(PendingRequest $pendingRequest, RequestInterface $psrRequest): void
    {
        $headers = [];

        foreach ($psrRequest->getHeaders() as $headerName => $value) {
            $headers[$headerName] = implode(';', $value);
        }

        $requestClass = get_class($pendingRequest->getRequest());

        // @todo see into improving this.
        VarDumper::dump(
            [
                'connector' => get_class($pendingRequest->getConnector()),
                'request' => $requestClass,
                'method' => $psrRequest->getMethod(),
                'uri' => (string) $psrRequest->getUri(),
                'headers' => $headers,
                'body' => (string) $psrRequest->getBody(),
            ]
        );
    }

    /**
     * Debug a response with Symfony Var Dumper
     */
    public static function symfonyResponseDebugger(Response $response, ResponseInterface $psrResponse): void
    {
        $headers = [];

        foreach ($psrResponse->getHeaders() as $headerName => $value) {
            $headers[$headerName] = implode(';', $value);
        }

        $requestClass = get_class($response->getRequest());

        // @todo see into improving this.
        VarDumper::dump(
            [
                'status' => $response->status(),
                'headers' => $headers,
                'body' => $response->body(),
            ]
        );
    }

    /**
     * Kill the application
     *
     * This is a method as it can be easily mocked during tests
     */
    public static function die(): void
    {
        $handler = self::$dieHandler ?? static fn () => exit(1);

        $handler();
    }
}
