<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Responses;

use Salette\Http\Response;

class UserResponse extends Response
{
    /**
     * @return \Salette\Tests\Fixtures\Responses\UserData
     * @throws \JsonException
     */
    public function customCastMethod()
    {
        return new UserData($this->json('foo'));
    }

    /**
     * @throws \JsonException
     */
    public function foo(): ?string
    {
        return $this->json('foo');
    }
}
