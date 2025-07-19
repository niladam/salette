<?php

declare(strict_types=1);

namespace Salette\Http\Middleware;

use Salette\Contracts\ResponseMiddleware;
use Salette\Data\RecordedResponse;
use Salette\Exceptions\FixtureException;
use Salette\Http\Faking\Fixture;
use Salette\Http\Faking\MockClient;
use Salette\Http\Response;

class RecordFixture implements ResponseMiddleware
{
    /**
     * The Fixture
     */
    protected Fixture $fixture;

    /**
     * Mock Client
     */
    protected MockClient $mockClient;

    public function __construct(Fixture $fixture, MockClient $mockClient)
    {
        $this->fixture = $fixture;
        $this->mockClient = $mockClient;
    }

    /**
     * Store the response
     *
     * @throws \JsonException
     * @throws FixtureException
     */
    public function __invoke(Response $response): void
    {
        $this->fixture->store(
            RecordedResponse::fromResponse($response)
        );

        $this->mockClient->recordResponse($response);
    }
}
