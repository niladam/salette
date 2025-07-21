<?php

declare(strict_types=1);

namespace Salette\Helpers;

use Salette\Data\Pipe;
use Salette\Enums\PipeOrder;
use Salette\Exceptions\DuplicatePipeNameException;

class Pipeline
{
    /**
     * The pipes in the pipeline.
     *
     * @var Pipe[]
     */
    protected array $pipes = [];

    /**
     * Add a pipe to the pipeline.
     *
     * @throws DuplicatePipeNameException
     */
    public function pipe(callable $callable, ?string $name = null, ?PipeOrder $order = null): self
    {
        $pipe = new Pipe($callable, $name, $order);

        // Only check for duplicate names if a name is provided
        if (is_string($name) && $this->pipeExists($name)) {
            throw new DuplicatePipeNameException($name);
        }

        $this->pipes[] = $pipe;

        return $this;
    }

    /**
     * Process the pipeline.
     *
     * @param  mixed $payload
     * @return mixed
     */
    public function process($payload)
    {
        foreach ($this->sortPipes() as $pipe) {
            $payload = call_user_func($pipe->callable, $payload);
        }

        return $payload;
    }

    /**
     * Sort the pipes based on their order.
     *
     * @return Pipe[]
     */
    protected function sortPipes(): array
    {
        $firstPipes = [];
        $nullPipes = [];
        $lastPipes = [];

        foreach ($this->pipes as $pipe) {
            $order = $pipe->order;

            if ($order instanceof PipeOrder && $order->value() === PipeOrder::FIRST) {
                $firstPipes[] = $pipe;
            } elseif ($order instanceof PipeOrder && $order->value() === PipeOrder::LAST) {
                $lastPipes[] = $pipe;
            } else {
                $nullPipes[] = $pipe;
            }
        }

        return array_merge($firstPipes, $nullPipes, $lastPipes);
    }

    /**
     * Set the pipes on the pipeline.
     *
     * @param Pipe[] $pipes
     *
     * @throws DuplicatePipeNameException
     */
    public function setPipes(array $pipes): self
    {
        $this->pipes = [];

        foreach ($pipes as $pipe) {
            $this->pipe($pipe->callable, $pipe->name, $pipe->order);
        }

        return $this;
    }

    /**
     * Get all the pipes in the pipeline.
     *
     * @return Pipe[]
     */
    public function getPipes(): array
    {
        return $this->pipes;
    }

    /**
     * Check if a given pipe name exists.
     */
    protected function pipeExists(string $name): bool
    {
        foreach ($this->pipes as $pipe) {
            if ($pipe->name === $name) {
                return true;
            }
        }

        return false;
    }
}
