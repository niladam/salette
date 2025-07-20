<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Connectors\WithBootConnector;

class UserRequestWithBoot extends Request
{
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
    protected string $connector = WithBootConnector::class;
    protected string $farewell = 'Ride on, cowboy.';

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }

    public function boot(Request $request)
    {
        $this->addHeader('X-Request-Boot-Header', 'Yee-haw!');
        $this->addHeader('X-Request-Boot-With-Data', $request->farewell);
    }

    
    public function __construct(string $farewell = 'Ride on, cowboy.')
    {
        $this->farewell = $farewell;
        //
    }
}
