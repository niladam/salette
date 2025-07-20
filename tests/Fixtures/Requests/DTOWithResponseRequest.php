<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Http\Response;
use Salette\Tests\Fixtures\Data\User;
use Salette\Tests\Fixtures\Data\UserWithResponse;
use Salette\Tests\Fixtures\Connectors\TestConnector;

class DTOWithResponseRequest extends Request
{
    /**
     * Define the method that the request will use.
     *
     * @var string
     */
    public const METHOD = Method::GET;

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

    public function __construct($userId = null, $groupId = null)
    {
        //
    }

    /**
     * Cast to a User.
     */
    public function createDtoFromResponse(Response $response)
    {
        return UserWithResponse::fromResponse($response);
    }
}
