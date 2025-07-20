<?php

declare(strict_types=1);

use Salette\Auth\AccessTokenAuthenticator;
use Salette\Exceptions\OAuthConfigValidationException;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Http\Response;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Connectors\ClientCredentialsBasicAuthConnector;
use Salette\Tests\Fixtures\Connectors\ClientCredentialsConnector;
use Salette\Tests\Fixtures\Connectors\CustomRequestClientCredentialsConnector;
use Salette\Tests\Fixtures\Connectors\NoConfigClientCredentialsConnector;
use Salette\Tests\Fixtures\Requests\OAuth\CustomClientCredentialsAccessTokenRequest;

test('you can get the authenticator from the connector', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'expires_in' => 3600], 200),
    ]);

    $connector = new ClientCredentialsConnector();
    $connector->withMockClient($mockClient);

    $authenticator = $connector->getAccessToken();

    expect($authenticator)
        ->toBeInstanceOf(AccessTokenAuthenticator::class)
        ->and($authenticator->getAccessToken())->toEqual('access')
        ->and($authenticator->getRefreshToken())->toBeNull()
        ->and($authenticator->isRefreshable())->toBeFalse()
        ->and($authenticator->getExpiresAt())->toBeInstanceOf(DateTimeImmutable::class);

    $mockClient->assertSentCount(1);

    expect($mockClient->getLastPendingRequest()->body()->all())->toEqual([
        'grant_type' => 'client_credentials',
        'client_id' => 'client-id',
        'client_secret' => 'client-secret',
        'scope' => '',
    ]);
});

test('you can get the response instead of the authenticator', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'expires_in' => 3600], 200),
    ]);

    $connector = new ClientCredentialsConnector();
    $connector->withMockClient($mockClient);

    $response = $connector->getAccessToken([], ' ', true);

    expect($response)
        ->toBeInstanceOf(Response::class)
        ->and($response->json())->toEqual([
            'access_token' => 'access',
            'expires_in' => 3600,
        ]);
});

test('you can tap into the token request', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'expires_in' => 3600], 200),
    ]);

    $connector = new ClientCredentialsConnector();
    $connector->withMockClient($mockClient);

    $authenticator = $connector->getAccessToken([], ' ', false, function (Request $request) {
        $request->query()->add('yee', 'haw');
    });

    expect($authenticator)
        ->toBeInstanceOf(AccessTokenAuthenticator::class)
        ->and($authenticator->getAccessToken())->toEqual('access')
        ->and($authenticator->getRefreshToken())->toBeNull()
        ->and($authenticator->isRefreshable())->toBeFalse()
        ->and($authenticator->getExpiresAt())->toBeInstanceOf(DateTimeImmutable::class);

    $mockClient->assertSentCount(1);

    expect($mockClient->getLastPendingRequest()->query()->all())->toEqual(['yee' => 'haw']);
});

test('you can send scopes with the token request', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'expires_in' => 3600], 200),
    ]);

    $connector = new ClientCredentialsConnector();
    $connector->withMockClient($mockClient);

    $authenticator = $connector->getAccessToken(['offline_access', 'clients', 'billing']);

    expect($authenticator)->toBeInstanceOf(AccessTokenAuthenticator::class);

    $mockClient->assertSentCount(1);

    expect($mockClient->getLastPendingRequest()->body()->all())->toEqual([
        'grant_type' => 'client_credentials',
        'client_id' => 'client-id',
        'client_secret' => 'client-secret',
        'scope' => 'offline_access clients billing',
    ]);
});

test('default scopes on the oauth config will be merged in with the scopes on the token request', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'expires_in' => 3600], 200),
    ]);

    $connector = new ClientCredentialsConnector();
    $connector->withMockClient($mockClient);

    $connector->oauthConfig()->setDefaultScopes([
        'compliance',
    ]);

    $authenticator = $connector->getAccessToken(['offline_access', 'clients', 'billing']);

    expect($authenticator)->toBeInstanceOf(AccessTokenAuthenticator::class);

    $mockClient->assertSentCount(1);

    expect($mockClient->getLastPendingRequest()->body()->all())->toEqual([
        'grant_type' => 'client_credentials',
        'client_id' => 'client-id',
        'client_secret' => 'client-secret',
        'scope' => 'compliance offline_access clients billing',
    ]);
});

test('the scope separator can be customised', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'expires_in' => 3600], 200),
    ]);

    $connector = new ClientCredentialsConnector();
    $connector->withMockClient($mockClient);

    $connector->oauthConfig()->setDefaultScopes([
        'compliance',
    ]);

    $authenticator = $connector->getAccessToken(['offline_access', 'clients', 'billing'], '+');

    expect($authenticator)->toBeInstanceOf(AccessTokenAuthenticator::class);

    $mockClient->assertSentCount(1);

    expect($mockClient->getLastPendingRequest()->body()->all())->toEqual([
        'grant_type' => 'client_credentials',
        'client_id' => 'client-id',
        'client_secret' => 'client-secret',
        'scope' => 'compliance+offline_access+clients+billing',
    ]);
});

test('if you attempt to use the client credentials flow without a client id it will throw an exception', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'expires_in' => 3600], 200),
    ]);

    $connector = new NoConfigClientCredentialsConnector();
    $connector->withMockClient($mockClient);

    $this->expectException(OAuthConfigValidationException::class);
    $this->expectExceptionMessage('The Client ID is empty or has not been provided.');

    $connector->getAccessToken();
});

test('if you attempt to use the client credentials flow without a secret it will throw an exception', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'expires_in' => 3600], 200),
    ]);

    $connector = new NoConfigClientCredentialsConnector();
    $connector->withMockClient($mockClient);

    $connector->oauthConfig()->setClientId('hello');

    $this->expectException(OAuthConfigValidationException::class);
    $this->expectExceptionMessage('The Client Secret is empty or has not been provided.');

    $connector->getAccessToken();
});

test('on the connector you can overwrite the getAccessToken request', function () {
    $mockClient = new MockClient([
        CustomClientCredentialsAccessTokenRequest::class =>
            MockResponse::make(
                ['access_token' => 'access', 'expires_in' => 3600],
                200
            ),
    ]);

    $connector = new CustomRequestClientCredentialsConnector();
    $connector->withMockClient($mockClient);

    $accessTokenResponse = $connector->getAccessToken([], ' ',  true);

    expect($accessTokenResponse->getRequest())->toBeInstanceOf(CustomClientCredentialsAccessTokenRequest::class);
});

test('the client credentials grant can use basic auth', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'expires_in' => 3600], 200),
    ]);

    $connector = new ClientCredentialsBasicAuthConnector();
    $connector->withMockClient($mockClient);

    $authenticator = $connector->getAccessToken();

    expect($authenticator)
        ->toBeInstanceOf(AccessTokenAuthenticator::class)
        ->and($authenticator->getAccessToken())->toEqual('access')
        ->and($authenticator->getRefreshToken())->toBeNull()
        ->and($authenticator->isRefreshable())->toBeFalse()
        ->and($authenticator->getExpiresAt())->toBeInstanceOf(DateTimeImmutable::class);

    $mockClient->assertSentCount(1);

    expect($mockClient->getLastPendingRequest()->body()->all())
        ->toEqual([
            'grant_type' => 'client_credentials',
            'scope' => '',
        ])
        ->and($mockClient->getLastPendingRequest()->headers()->get('Authorization'))
        ->toEqual('Basic ' . base64_encode('client-id:client-secret'));
});
