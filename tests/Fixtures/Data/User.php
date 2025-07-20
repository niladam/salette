<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Data;

use Salette\Http\Response;

class User
{
    public string $actualName;

    public string $name;

    public string $twitter;

    public function __construct(
        string $name,
        string $actualName,
        string $twitter
    ) {
        $this->twitter = $twitter;
        $this->name = $name;
        $this->actualName = $actualName;
        //
    }

    /**
     * @return static
     */
    public function fromSaloon(Response $response): self
    {
        $data = $response->json();

        return new static($data['name'], $data['actual_name'], $data['twitter']);
    }
}
