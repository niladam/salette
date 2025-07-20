<?php

declare(strict_types=1);

use Salette\Exceptions\OAuthConfigValidationException;
use Salette\Helpers\OAuth2\OAuthConfig;

test('all default properties are correct and all getters and setters work properly', function () {
    $config = new OAuthConfig();

    expect($config->getClientId())
        ->toEqual('')
        ->and($config->getClientSecret())->toEqual('')
        ->and($config->getRedirectUri())->toEqual('')
        ->and($config->getAuthorizeEndpoint())->toEqual('authorize')
        ->and($config->getTokenEndpoint())->toEqual('token')
        ->and($config->getUserEndpoint())->toEqual('user')
        ->and($config->getDefaultScopes())->toEqual([]);

    $clientId = 'client-id';
    $clientSecret = 'client-secret';
    $redirectUri = 'https://my-app.saloon.dev/auth/callback';
    $authorizeEndpoint = 'auth/authorize';
    $tokenEndpoint = 'auth/token';
    $userEndpoint = 'auth/user';
    $defaultScopes = ['profile'];

    expect($config->setClientId($clientId))
        ->toEqual($config)
        ->and($config->setClientSecret($clientSecret))->toEqual($config)
        ->and($config->setRedirectUri($redirectUri))->toEqual($config)
        ->and($config->setAuthorizeEndpoint($authorizeEndpoint))->toEqual($config)
        ->and($config->setTokenEndpoint($tokenEndpoint))->toEqual($config)
        ->and($config->setUserEndpoint($userEndpoint))->toEqual($config)
        ->and($config->setDefaultScopes($defaultScopes))->toEqual($config)
        ->and($config->getClientId())->toEqual($clientId)
        ->and($config->getClientSecret())->toEqual($clientSecret)
        ->and($config->getRedirectUri())->toEqual($redirectUri)
        ->and($config->getAuthorizeEndpoint())->toEqual($authorizeEndpoint)
        ->and($config->getTokenEndpoint())->toEqual($tokenEndpoint)
        ->and($config->getUserEndpoint())->toEqual($userEndpoint)
        ->and($config->getDefaultScopes())->toEqual($defaultScopes);
});

test('make method creates an instance of OAuthConfig', function () {
    expect(OAuthConfig::make())->toBeInstanceOf(OAuthConfig::class);
});

test('it will throw an exception if you do not specify the client id', function () {
    $config = new OAuthConfig();
    $config->validate();
})->throws(OAuthConfigValidationException::class, 'The Client ID is empty or has not been provided.');

test('it will throw an exception if you do not specify the client secret', function () {
    $config = new OAuthConfig();
    $config->setClientId('client-id');

    $config->validate();
})->throws(OAuthConfigValidationException::class, 'The Client Secret is empty or has not been provided.');

test('it will throw an exception if you do not specify the redirect uri', function () {
    $config = new OAuthConfig();

    $config->setClientId('client-id')
        ->setClientSecret('client-secret');

    $config->validate();
})->throws(OAuthConfigValidationException::class, 'The Redirect URI is empty or has not been provided.');
