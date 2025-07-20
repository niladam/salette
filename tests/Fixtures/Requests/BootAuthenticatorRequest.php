<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Contracts\Body\HasBody;
use Salette\Enums\Method;
use Salette\Requests\PendingRequest;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Connectors\TestConnector;
use Salette\Traits\Body\HasJsonBody;

class BootAuthenticatorRequest extends Request implements HasBody
{
    use HasJsonBody;

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

    public function resolveEndpoint(): string
    {
        return '/user';
    }

    public function boot(PendingRequest $pendingRequest):void
    {
        $pendingRequest->withTokenAuth('howdy-partner');
    }
}
