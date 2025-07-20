<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Responses\UserResponseNoExtendSaloonResponse;

class InvalidResponseClass extends Request
{
    /**
     * Define the method that the request will use.
     *
     * @var string
     */
    public const METHOD = Method::GET;

    /**
     * Define the custom response
     */
    protected ?string $response = UserResponseNoExtendSaloonResponse::class;

    /**
     * The connector.
     */
    protected string $connector = TestConnector::class;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }
}
