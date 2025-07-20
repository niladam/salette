<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Authenticators;

use DateTimeImmutable;
use Salette\Auth\AccessTokenAuthenticator;

class CustomOAuthAuthenticator extends AccessTokenAuthenticator
{
    public string $accessToken;

    public string $greeting;

    public ?string $refreshToken = null;

    public ?DateTimeImmutable $expiresAt = null;

    /**
     * Constructor
     */
    public function __construct(
        string $accessToken,
        string $greeting,
        $refreshToken = null,
        $expiresAt = null,
    ) {
        $this->expiresAt = $expiresAt;
        $this->refreshToken = $refreshToken;
        $this->greeting = $greeting;
        $this->accessToken = $accessToken;
        //
    }

    public function getGreeting(): string
    {
        return $this->greeting;
    }
}
