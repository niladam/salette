<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Tests\Fixtures\Resources\UserBaseResource;

class ResourceConnector extends TestConnector
{
    public function user()
    {
        return new UserBaseResource($this);
    }
}
