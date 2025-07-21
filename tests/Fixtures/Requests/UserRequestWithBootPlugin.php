<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Tests\Fixtures\Plugins\WithBootTestPlugin;

class UserRequestWithBootPlugin extends Request
{
    use WithBootTestPlugin;

    /**
     * Define the method that the request will use.
     *
     * @var string|null
     */
    public const METHOD = Method::GET;

    /**
     * The connector.
     *
     * @var string|null
     */
    protected string $connector = TestConnector::class;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }

    public $userId;
    public $groupId;

    public function __construct($userId = null, $groupId = null)
    {
        $this->userId = $userId;
        $this->groupId = $groupId;
    }
}
