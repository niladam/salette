<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Data;

use Salette\Contracts\DataObjects\WithResponse;
use Salette\Http\Response;
use Salette\Traits\Responses\HasResponse;

class UserWithResponse implements WithResponse
{
    use HasResponse;

    public string $name;

    public string $actualName;

    public string $twitter;

    public function __construct(
        string $name,
        string $actualName,
        string $twitter
    ) {
        $this->twitter = $twitter;
        $this->actualName = $actualName;
        $this->name = $name;
    }

    public static function fromResponse(Response $response): self
    {
        $data = $response->json();

        return new self($data['name'], $data['actual_name'], $data['twitter']);
    }
}
