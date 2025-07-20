<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;

class UserRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    public const METHOD = Method::GET;

    public $userId;
    public $groupId;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }

    public function __construct($userId = null, $groupId = null)
    {
        $this->userId = $userId;
        $this->groupId = $groupId;
    }
}
