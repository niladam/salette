<?php

declare(strict_types=1);

namespace Salette\Auth;

use GuzzleHttp\RequestOptions;
use Salette\Contracts\Authenticator;
use Salette\Exceptions\SaletteException;
use Salette\Requests\PendingRequest;
use Salette\Senders\GuzzleSender;

class CertificateAuthenticator implements Authenticator
{
    public string $path;

    public ?string $password;

    public function __construct(string $path, ?string $password = null)
    {
        $this->path = $path;
        $this->password = $password;
    }

    /**
     * Apply the authentication to the request.
     *
     * @throws SaletteException
     */
    public function set(PendingRequest $pendingRequest): void
    {
        if (! $pendingRequest->getConnector()->sender() instanceof GuzzleSender) {
            throw new SaletteException('The CertificateAuthenticator is only supported when using the GuzzleSender.');
        }

        // See: https://docs.guzzlephp.org/en/stable/request-options.html#cert

        $path = $this->path;
        $password = $this->password;

        $certificate = is_string($password) ? [$path, $password] : $path;

        $pendingRequest->config()->add(RequestOptions::CERT, $certificate);
    }
}
