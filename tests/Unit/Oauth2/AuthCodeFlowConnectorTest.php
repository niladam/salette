<?php

declare(strict_types=1);

use Salette\Auth\AccessTokenAuthenticator;
use Salette\Exceptions\OAuthConfigValidationException;
use Salette\Helpers\OAuth2\OAuthConfig;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Tests\Fixtures\Connectors\OAuth2Connector;
use Salette\Tests\Helpers\Date;

test('the oauth 2 config class can be configured properly', function () {
    $connector = new OAuth2Connector();

    $config = $connector->oauthConfig();

    expect($config)
        ->toBeInstanceOf(OAuthConfig::class)
        ->and($config->getClientId())->toEqual('client-id')
        ->and($config->getClientSecret())->toEqual('client-secret')
        ->and($config->getRedirectUri())->toEqual('https://my-app.saloon.dev/auth/callback');
});

test('the oauth config is validated when generating an authorization url', function () {
    $connector = new OAuth2Connector();
    $connector->oauthConfig()->setClientId('');

    $connector->getAuthorizationUrl();
})->throws(OAuthConfigValidationException::class, 'The Client ID is empty or has not been provided.');

test('the oauth config is validated when creating access tokens', function () {
    $connector = new OAuth2Connector();
    $connector->oauthConfig()->setClientId('');

    $connector->getAccessToken('code');
})->throws(OAuthConfigValidationException::class, 'The Client ID is empty or has not been provided.');

test('the oauth config is validated when refreshing access tokens', function () {
    $connector = new OAuth2Connector();
    $connector->oauthConfig()->setClientId('');

    $connector->refreshAccessToken('');
})->throws(OAuthConfigValidationException::class, 'The Client ID is empty or has not been provided.');

test('the old refresh token is carried over if a response does not include a new refresh token', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access-new', 'expires_in' => 3600]),
    ]);

    $connector = new OAuth2Connector();

    $connector->withMockClient($mockClient);

    $authenticator = new AccessTokenAuthenticator(
        'access',
        'refresh-old',
        Date::now()->addSeconds(3600)->toDateTime()
    );

    $newAuthenticator = $connector->refreshAccessToken($authenticator);

    expect($newAuthenticator->getRefreshToken())->toEqual('refresh-old');
});

test('the old refresh token is carried over if a response does not include a new refresh token and the refresh is a string', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access-new', 'expires_in' => 3600]),
    ]);

    $connector = new OAuth2Connector();

    $connector->withMockClient($mockClient);

    $newAuthenticator = $connector->refreshAccessToken('refresh-old');

    expect($newAuthenticator->getRefreshToken())->toEqual('refresh-old');
});
