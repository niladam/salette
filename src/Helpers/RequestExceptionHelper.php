<?php

declare(strict_types=1);

namespace Salette\Helpers;

use Salette\Exceptions\ClientException;
use Salette\Exceptions\RequestException;
use Salette\Exceptions\ServerException;
use Salette\Exceptions\Statuses\ForbiddenException;
use Salette\Exceptions\Statuses\GatewayTimeoutException;
use Salette\Exceptions\Statuses\InternalServerErrorException;
use Salette\Exceptions\Statuses\MethodNotAllowedException;
use Salette\Exceptions\Statuses\NotFoundException;
use Salette\Exceptions\Statuses\PaymentRequiredException;
use Salette\Exceptions\Statuses\RequestTimeOutException;
use Salette\Exceptions\Statuses\ServiceUnavailableException;
use Salette\Exceptions\Statuses\TooManyRequestsException;
use Salette\Exceptions\Statuses\UnauthorizedException;
use Salette\Exceptions\Statuses\UnprocessableEntityException;
use Salette\Http\Response;
use Throwable;

class RequestExceptionHelper
{
    /**
     * Map of HTTP status codes to exception class names.
     *
     * @var array<int, class-string<RequestException>>
     */
    protected static array $map = [
        401 => UnauthorizedException::class,
        402 => PaymentRequiredException::class,
        403 => ForbiddenException::class,
        404 => NotFoundException::class,
        405 => MethodNotAllowedException::class,
        408 => RequestTimeOutException::class,
        422 => UnprocessableEntityException::class,
        429 => TooManyRequestsException::class,
        500 => InternalServerErrorException::class,
        503 => ServiceUnavailableException::class,
        504 => GatewayTimeoutException::class,
    ];

    public static function create(Response $response, ?Throwable $previous = null): RequestException
    {
        $status = $response->status();

        if (isset(static::$map[$status])) {
            $exceptionClass = static::$map[$status];
        } elseif ($response->serverError()) {
            $exceptionClass = ServerException::class;
        } elseif ($response->clientError()) {
            $exceptionClass = ClientException::class;
        } else {
            $exceptionClass = RequestException::class;
        }

        return new $exceptionClass($response, null, 0, $previous);
    }
}
