<?php

declare(strict_types=1);

namespace Salette\Traits;

use Salette\Http\Faking\MockClient;

trait HasMockClient
{
    /**
     * Mock Client
     */
    protected ?MockClient $mockClient = null;

    /**
     * Specify a mock client.
     *
     * @return $this
     */
    public function withMockClient(MockClient $mockClient): self
    {
        $this->mockClient = $mockClient;

        return $this;
    }

    /**
     * Get the mock client.
     */
    public function getMockClient(): ?MockClient
    {
        return $this->mockClient;
    }

    /**
     * Determine if the instance has a mock client
     */
    public function hasMockClient(): bool
    {
        return $this->mockClient instanceof MockClient;
    }
}
