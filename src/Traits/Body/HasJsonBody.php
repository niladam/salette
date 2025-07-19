<?php

declare(strict_types=1);

namespace Salette\Traits\Body;

use Salette\Repositories\JsonBodyRepository;
use Salette\Requests\PendingRequest;

/**
 * @phpstan-ignore trait.unused
 */
trait HasJsonBody
{
    use ChecksForHasBody;

    /**
     * Body Repository
     */
    protected JsonBodyRepository $body;

    /**
     * Boot the plugin
     */
    public function bootHasJsonBody(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('Content-Type', 'application/json');
    }

    /**
     * Retrieve the data repository
     */
    public function body(): JsonBodyRepository
    {
        return $this->body ??= new JsonBodyRepository($this->defaultBody());
    }

    /**
     * Default body
     *
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [];
    }
}
