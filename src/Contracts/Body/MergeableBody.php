<?php

declare(strict_types=1);

namespace Salette\Contracts\Body;

interface MergeableBody
{
    /**
     * Merge one or more associative arrays into this body.
     *
     * @param array<string,mixed> ...$arrays
     */
    public function merge(array ...$arrays): self;
}
