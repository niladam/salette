<?php

declare(strict_types=1);

namespace Salette\Data;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Salette\Contracts\MultipartBodyFactory;

class FactoryCollection
{
    public StreamFactoryInterface $streamFactory;

    public RequestFactoryInterface $requestFactory;

    public UriFactoryInterface $uriFactory;

    public ResponseFactoryInterface $responseFactory;

    public MultipartBodyFactory $multipartBodyFactory;

    public function __construct(
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory,
        StreamFactoryInterface $streamFactory,
        ResponseFactoryInterface $responseFactory,
        MultipartBodyFactory $multipartBodyFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->uriFactory = $uriFactory;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->multipartBodyFactory = $multipartBodyFactory;
    }
}
