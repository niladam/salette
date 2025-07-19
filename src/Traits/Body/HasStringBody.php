<?php

declare(strict_types=1);

namespace Salette\Traits\Body;

use Salette\Repositories\StringBodyRepository;

/**
 * @phpstan-ignore trait.unused
 */
trait HasStringBody
{
    use ChecksForHasBody;

    /**
     * Body Repository
     */
    protected StringBodyRepository $body;

    /**
     * Retrieve the data repository
     */
    public function body(): StringBodyRepository
    {
        return $this->body ??= new StringBodyRepository($this->defaultBody());
    }

    /**
     * Default body
     */
    protected function defaultBody(): ?string
    {
        return null;
    }
}
