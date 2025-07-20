<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Resources;

use Salette\Http\BaseResource;
use Salette\Tests\Fixtures\Requests\UserRequest;

class UserBaseResource extends BaseResource
{
    /**
     * Get User
     *
     * @throws \JsonException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function get()
    {
        return $this->connector->send(new UserRequest())->array();
    }
}
