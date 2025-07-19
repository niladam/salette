<?php

declare(strict_types=1);

namespace Salette\Exceptions;

class InvalidPoolItemException extends SaletteException
{
    public function __construct()
    {
        parent::__construct('You have provided an invalid request type into the pool. 
        The pool instance only accepts instances of Salette\Requests\Request or GuzzleHttp\Promise\PromiseInterface. 
        You may provide an array, a generator or a callable that provides an array or generator.');
    }
}
