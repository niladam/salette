<?php

declare(strict_types=1);

namespace Salette\Traits\Auth;

use Salette\Auth\BasicAuthenticator;
use Salette\Auth\CertificateAuthenticator;
use Salette\Auth\DigestAuthenticator;
use Salette\Auth\HeaderAuthenticator;
use Salette\Auth\QueryAuthenticator;
use Salette\Auth\TokenAuthenticator;
use Salette\Contracts\Authenticator;

trait AuthenticatesRequests
{
    protected ?Authenticator $authenticator = null;

    /**
     * Default authenticator used.
     */
    protected function defaultAuth(): ?Authenticator
    {
        return null;
    }

    /**
     * Retrieve the authenticator.
     */
    public function getAuthenticator(): ?Authenticator
    {
        return $this->authenticator ?? $this->defaultAuth();
    }

    /**
     * Authenticate the request with an authenticator.
     */
    public function authenticate(Authenticator $authenticator): self
    {
        $this->authenticator = $authenticator;

        return $this;
    }

    /**
     * Authenticate the request with an Authorization header.
     *
     * @deprecated This method should no longer be used.
     * You should use the defaultAuth method or the `->authenticate(new TokenAuthenticator)` instead.
     */
    public function withTokenAuth(string $token, string $prefix = 'Bearer'): self
    {
        return $this->authenticate(new TokenAuthenticator($token, $prefix));
    }

    /**
     * Authenticate the request with "basic" authentication.
     *
     * @deprecated This should no longer be used.
     * You should use the defaultAuth method or the `->authenticate(new BasicAuthenticator)` instead.
     *
     * @return $this
     */
    public function withBasicAuth(string $username, string $password): self
    {
        return $this->authenticate(new BasicAuthenticator($username, $password));
    }

    /**
     * Authenticate the request with "digest" authentication.
     *
     * @deprecated This should no longer be used.
     * You should use the defaultAuth method or the `->authenticate(new DigestAuthenticator)` instead.
     *
     * @return $this
     */
    public function withDigestAuth(string $username, string $password, string $digest): self
    {
        return $this->authenticate(new DigestAuthenticator($username, $password, $digest));
    }

    /**
     * Authenticate the request with a query parameter token.
     *
     * @deprecated This should no longer be used.
     * You should use the defaultAuth method or the `->authenticate(new QueryAuthenticator)` instead.
     *
     * @return $this
     */
    public function withQueryAuth(string $parameter, string $value): self
    {
        return $this->authenticate(new QueryAuthenticator($parameter, $value));
    }

    /**
     * Authenticate the request with a header.
     *
     * @deprecated This should no longer be used.
     * You should use the defaultAuth method or the `->authenticate(new HeaderAuthenticator)` instead.
     *
     * @return $this
     */
    public function withHeaderAuth(string $accessToken, string $headerName = 'Authorization'): self
    {
        return $this->authenticate(new HeaderAuthenticator($accessToken, $headerName));
    }

    /**
     * Authenticate the request with a certificate.
     *
     * @deprecated This should no longer be used.
     * You should use the defaultAuth method or the `->authenticate(new CertificateAuthenticator)` instead.
     *
     * @return $this
     */
    public function withCertificateAuth(string $path, ?string $password = null): self
    {
        return $this->authenticate(new CertificateAuthenticator($path, $password));
    }
}
