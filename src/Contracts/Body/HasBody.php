<?php

declare(strict_types=1);

namespace Salette\Contracts\Body;

use Salette\Contracts\BodyRepository;

interface HasBody
{
    /**
     * Define Data
     */
    public function body(): BodyRepository;
}
