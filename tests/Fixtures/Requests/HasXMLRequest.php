<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Contracts\Body\HasBody;
use Salette\Traits\Body\HasXmlBody;
use Salette\Tests\Fixtures\Connectors\TestConnector;

class HasXMLRequest extends Request implements HasBody
{
    use HasXmlBody;

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

    protected function defaultBody(): ?string
    {
        return '<xml></xml>';
    }
}
