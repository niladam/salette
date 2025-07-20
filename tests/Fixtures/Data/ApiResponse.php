<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Data;

use Salette\Http\Response;

class ApiResponse
{

    public array $data;

    public function __construct(
        array $data,
    ) {
        $this->data = $data;
        //
    }

    /**
     * @return static
     */
    public static function fromSalette(Response $response)
    {
        return new static($response->json());
    }
}
