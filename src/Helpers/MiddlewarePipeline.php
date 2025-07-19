<?php

declare(strict_types=1);

namespace Salette\Helpers;

use Salette\Enums\PipeOrder;
use Salette\Exceptions\DuplicatePipeNameException;
use Salette\Exceptions\FatalRequestException;
use Salette\Http\Response;
use Salette\Requests\PendingRequest;

class MiddlewarePipeline
{
    protected Pipeline $requestPipeline;

    protected Pipeline $responsePipeline;

    protected Pipeline $fatalPipeline;

    public function __construct()
    {
        $this->requestPipeline = new Pipeline();
        $this->responsePipeline = new Pipeline();
        $this->fatalPipeline = new Pipeline();
    }

    /**
     * @throws DuplicatePipeNameException
     */
    public function onRequest(callable $callable, ?string $name = null, ?PipeOrder $order = null): self
    {
        $this->requestPipeline->pipe(
            static function (PendingRequest $pendingRequest) use ($callable): PendingRequest {
                $result = $callable($pendingRequest);

                return $result instanceof PendingRequest ? $result : $pendingRequest;
            },
            $name,
            $order
        );

        return $this;
    }

    public function onResponse(callable $callable, ?string $name = null, ?PipeOrder $order = null): self
    {
        $this->responsePipeline->pipe(
            static function (Response $response) use ($callable): Response {
                $result = $callable($response);

                return $result instanceof Response ? $result : $response;
            },
            $name,
            $order
        );

        return $this;
    }

    public function onFatalException(callable $callable, ?string $name = null, ?PipeOrder $order = null): self
    {
        $this->fatalPipeline->pipe(
            static function (FatalRequestException $throwable) use ($callable): FatalRequestException {
                $callable($throwable);

                return $throwable;
            },
            $name,
            $order
        );

        return $this;
    }

    public function executeRequestPipeline(PendingRequest $pendingRequest): PendingRequest
    {
        return $this->requestPipeline->process($pendingRequest);
    }

    public function executeResponsePipeline(Response $response): Response
    {
        return $this->responsePipeline->process($response);
    }

    /**
     * @throws FatalRequestException
     */
    public function executeFatalPipeline(FatalRequestException $throwable): void
    {
        $this->fatalPipeline->process($throwable);
    }

    /**
     * @throws DuplicatePipeNameException
     */
    public function merge(MiddlewarePipeline $middlewarePipeline): self
    {
        $this->requestPipeline->setPipes(
            array_merge(
                $this->requestPipeline->getPipes(),
                $middlewarePipeline->getRequestPipeline()->getPipes()
            )
        );

        $this->responsePipeline->setPipes(
            array_merge(
                $this->responsePipeline->getPipes(),
                $middlewarePipeline->getResponsePipeline()->getPipes()
            )
        );

        $this->fatalPipeline->setPipes(
            array_merge(
                $this->fatalPipeline->getPipes(),
                $middlewarePipeline->getFatalPipeline()->getPipes()
            )
        );

        return $this;
    }

    public function getRequestPipeline(): Pipeline
    {
        return $this->requestPipeline;
    }

    public function getResponsePipeline(): Pipeline
    {
        return $this->responsePipeline;
    }

    public function getFatalPipeline(): Pipeline
    {
        return $this->fatalPipeline;
    }
}
