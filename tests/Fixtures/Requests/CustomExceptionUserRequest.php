<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Requests;

use Salette\Enums\Method;
use Salette\Http\Response;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Exceptions\CustomRequestException;
use Throwable;

class CustomExceptionUserRequest extends Request
{
    /**
     * Define the HTTP method.
     *
     * @var string
     */
    public const METHOD = Method::GET;

    /**
     * Define the endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/user';
    }

    /**
     * Get the custom request exception
     */
    public function getRequestException(Response $response, $senderException): ?Throwable
    {
        return new CustomRequestException($response, 'Oh yee-naw.', 0, $senderException);
    }
}
