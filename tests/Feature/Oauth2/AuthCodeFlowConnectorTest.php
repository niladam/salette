<?php

declare(strict_types=1);

use Salette\Auth\AccessTokenAuthenticator;
use Salette\Exceptions\InvalidStateException;
use Salette\Exceptions\OAuthConfigValidationException;
use Salette\Http\Faking\MockClient;
use Salette\Http\Faking\MockResponse;
use Salette\Http\OAuth2\GetAccessTokenRequest;
use Salette\Http\OAuth2\GetRefreshTokenRequest;
use Salette\Http\OAuth2\GetUserRequest;
use Salette\Http\Response;
use Salette\Requests\Request;
use Salette\Tests\Fixtures\Authenticators\CustomOAuthAuthenticator;
use Salette\Tests\Fixtures\Connectors\CustomRequestOAuth2Connector;
use Salette\Tests\Fixtures\Connectors\CustomResponseOAuth2Connector;
use Salette\Tests\Fixtures\Connectors\NoConfigAuthCodeConnector;
use Salette\Tests\Fixtures\Connectors\OAuth2Connector;
use Salette\Tests\Fixtures\Requests\OAuth\CustomAccessTokenRequest;
use Salette\Tests\Fixtures\Requests\OAuth\CustomOAuthUserRequest;
use Salette\Tests\Fixtures\Requests\OAuth\CustomRefreshTokenRequest;
use Salette\Tests\Helpers\Date;

test('you can get the redirect url from a connector', function () {
    $connector = new OAuth2Connector();

    expect($connector->getState())->toBeNull();

    $url = $connector->getAuthorizationUrl(['scope-1', 'scope-2'], 'my-state');

    $state = $connector->getState();

    expect($state)
        ->toEqual('my-state')
        ->and($url)->toEqual(
            'https://oauth.saloon.dev/authorize?response_type=code&scope=scope-1%20scope-2&client_id=client-id&redirect_uri=https%3A%2F%2Fmy-app.saloon.dev%2Fauth%2Fcallback&state=my-state'
        );
});

test('you can provide default scopes that will be applied to every authorization url', function () {
    $connector = new OAuth2Connector();

    $connector->oauthConfig()->setDefaultScopes(['scope-3']);

    $url = $connector->getAuthorizationUrl(['scope-1', 'scope-2'], 'my-state');

    expect($url)->toEqual(
        'https://oauth.saloon.dev/authorize?response_type=code&scope=scope-3%20scope-1%20scope-2&client_id=client-id&redirect_uri=https%3A%2F%2Fmy-app.saloon.dev%2Fauth%2Fcallback&state=my-state'
    );
});

test('you can get authorization url without setting valid scopes', function () {
    $connector = new OAuth2Connector();

    $connector->oauthConfig()->setDefaultScopes(['', null]);

    $url = $connector->getAuthorizationUrl([], 'my-state');

    expect($url)->toEqual(
        'https://oauth.saloon.dev/authorize?response_type=code&client_id=client-id&redirect_uri=https%3A%2F%2Fmy-app.saloon.dev%2Fauth%2Fcallback&state=my-state'
    );
});

test('default state is generated automatically with every authorization url if state is not defined', function () {
    $connector = new OAuth2Connector();

    $connector->oauthConfig()->setDefaultScopes(['scope-3']);

    expect($connector->getState())->toBeNull();

    $url = $connector->getAuthorizationUrl(['scope-1', 'scope-2']);
    $state = $connector->getState();

    expect($state)->toBeString();

    expect(str_ends_with($url, $state))->toBeTrue();
});

test('additional query parameters can be added passed to the authorization url', function () {
    $connector = new OAuth2Connector();

    $url = $connector->getAuthorizationUrl(
        ['scope-1', 'scope-2'],
        null,
        'my-state',
        ['another-param' => 'another-value', 'yee' => 'haw']
    );

    expect(str_ends_with($url, 'another-param=another-value&yee=haw'))
        ->toBeTrue()
        ->and($url)->toEqual(
            'https://oauth.saloon.dev/authorize?response_type=code&scope=scope-1%20scope-2&client_id=client-id&redirect_uri=https%3A%2F%2Fmy-app.saloon.dev%2Fauth%2Fcallback&state=my-state&another-param=another-value&yee=haw'
        );
});

test('you can request a token from a connector', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'refresh_token' => 'refresh', 'expires_in' => 3600], 200),
    ]);

    $connector = new OAuth2Connector();

    $connector->withMockClient($mockClient);

    $authenticator = $connector->getAccessToken('code');

    expect($authenticator)
        ->toBeInstanceOf(AccessTokenAuthenticator::class)
        ->and($authenticator->getAccessToken())->toEqual('access')
        ->and($authenticator->getRefreshToken())->toEqual('refresh')
        ->and($authenticator->getExpiresAt())->toBeInstanceOf(DateTimeImmutable::class);
});

test(
    /**
     * @throws OAuthConfigValidationException
     * @throws Throwable
     * @throws \Salette\Exceptions\RequestException
     * @throws \Salette\Exceptions\FatalRequestException
     * @throws InvalidStateException
     */    'you can tap into the access token request and modify it',
    function () {
        $mockClient = new MockClient([
            MockResponse::make(['access_token' => 'access', 'refresh_token' => 'refresh', 'expires_in' => 3600], 200),
        ]);

        $connector = new OAuth2Connector();

        $connector->withMockClient($mockClient);

        $authenticator = $connector->getAccessToken(
            'code',
            null,
            null,
            false,
            function (Request $request) {
                $request->query()->add('yee', 'haw');
            }
        );

        expect($authenticator)
            ->toBeInstanceOf(AccessTokenAuthenticator::class)
            ->and($authenticator->getAccessToken())->toEqual('access')
            ->and($authenticator->getRefreshToken())->toEqual('refresh')
            ->and($authenticator->getExpiresAt())->toBeInstanceOf(DateTimeImmutable::class);

        $mockClient->assertSentCount(1);

        expect($mockClient->getLastPendingRequest()->query()->all())->toEqual(['yee' => 'haw']);
    }
);

test('you can request the original response instead of the authenticator on the create tokens method', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'refresh_token' => 'refresh', 'expires_in' => 3600]),
    ]);

    $connector = new OAuth2Connector();

    $connector->withMockClient($mockClient);

    $response = $connector->getAccessToken('code', null, null, true);

    expect($response)
        ->toBeInstanceOf(Response::class)
        ->and($response->json())->toEqual(
            ['access_token' => 'access', 'refresh_token' => 'refresh', 'expires_in' => 3600]
        );
});

test('it will throw an exception if state is invalid', function () {
    $connector = new OAuth2Connector();

    $state = 'secret';
    $url = $connector->getAuthorizationUrl(['scope-1', 'scope-2'], $state);

    $connector->getAccessToken('code', 'invalid', $state);
})->throws(InvalidStateException::class, 'Invalid state.');

test('you can refresh a token from a connector', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access-new', 'refresh_token' => 'refresh-new', 'expires_in' => 3600]),
    ]);

    $connector = new OAuth2Connector();

    $connector->withMockClient($mockClient);

    $authenticator = new AccessTokenAuthenticator('access', 'refresh', Date::now()->addSeconds(3600)->toDateTime());

    $newAuthenticator = $connector->refreshAccessToken($authenticator);

    expect($newAuthenticator)
        ->toBeInstanceOf(AccessTokenAuthenticator::class)
        ->and($newAuthenticator->getAccessToken())->toEqual('access-new')
        ->and($newAuthenticator->getRefreshToken())->toEqual('refresh-new')
        ->and($newAuthenticator->getExpiresAt())->toBeInstanceOf(DateTimeImmutable::class);
});

test('you can tap into the refresh token request', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access-new', 'refresh_token' => 'refresh-new', 'expires_in' => 3600]),
    ]);

    $connector = new OAuth2Connector();

    $connector->withMockClient($mockClient);

    $authenticator = new AccessTokenAuthenticator('access', 'refresh', Date::now()->addSeconds(3600)->toDateTime());

    $newAuthenticator = $connector->refreshAccessToken($authenticator, false, function (Request $request) {
        $request->query()->add('yee', 'haw');
    });

    expect($newAuthenticator)
        ->toBeInstanceOf(AccessTokenAuthenticator::class)
        ->and($newAuthenticator->getAccessToken())->toEqual('access-new')
        ->and($newAuthenticator->getRefreshToken())->toEqual('refresh-new')
        ->and($newAuthenticator->getExpiresAt())->toBeInstanceOf(DateTimeImmutable::class);

    $mockClient->assertSentCount(1);

    expect($mockClient->getLastPendingRequest()->query()->all())->toEqual(['yee' => 'haw']);
});

test('the refreshAccessToken method throws an exception if you provide it an authenticator that is not refreshable', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access-new', 'refresh_token' => 'refresh-new', 'expires_in' => 3600]),
    ]);

    $connector = new OAuth2Connector();

    $connector->withMockClient($mockClient);

    $authenticator = new AccessTokenAuthenticator('access', null, Date::now()->addSeconds(3600)->toDateTime());

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('The provided OAuthAuthenticator does not contain a refresh token.');

    $connector->refreshAccessToken($authenticator);
});

test('you can request the original response instead of the authenticator on the refresh tokens method', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access-new', 'refresh_token' => 'refresh-new', 'expires_in' => 3600]),
    ]);

    $connector = new OAuth2Connector();

    $connector->withMockClient($mockClient);

    $authenticator = new AccessTokenAuthenticator('access', 'refresh', Date::now()->addSeconds(3600)->toDateTime());

    $response = $connector->refreshAccessToken($authenticator, true);

    expect($response)
        ->toBeInstanceOf(Response::class)
        ->and($response->json())->toEqual(
            ['access_token' => 'access-new', 'refresh_token' => 'refresh-new', 'expires_in' => 3600]
        );
});

test('you can get the user from an oauth connector', function () {
    $mockClient = new MockClient([
        MockResponse::make(['user' => 'Sam']),
    ]);

    $connector = new OAuth2Connector();
    $connector->withMockClient($mockClient);

    $accessToken = new AccessTokenAuthenticator('access', 'refresh', Date::now()->addSeconds(3600)->toDateTime());

    $response = $connector->getUser($accessToken);

    expect($response)->toBeInstanceOf(Response::class);

    $pendingRequest = $response->getPendingRequest();

    expect($pendingRequest->headers()->all())->toEqual([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer access',
        'Content-Type' => 'application/x-www-form-urlencoded',
    ]);
});

test('you can tap into the the user request', function () {
    $mockClient = new MockClient([
        MockResponse::make(['user' => 'Sam']),
    ]);

    $connector = new OAuth2Connector();
    $connector->withMockClient($mockClient);

    $accessToken = new AccessTokenAuthenticator('access', 'refresh', Date::now()->addSeconds(3600)->toDateTime());

    $response = $connector->getUser($accessToken, function (Request $request) {
        $request->query()->add('yee', 'haw');
    });

    expect($response)->toBeInstanceOf(Response::class);

    $pendingRequest = $response->getPendingRequest();

    expect($pendingRequest->query()->all())
        ->toEqual(['yee' => 'haw'])
        ->and($pendingRequest->headers()->all())->toEqual([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer access',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
});

test('you can customize the oauth authenticator', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access-new', 'refresh_token' => 'refresh-new', 'expires_in' => 3600]),
    ]);

    $customConnector = new CustomResponseOAuth2Connector('Howdy!');
    $customConnector->withMockClient($mockClient);

    $authenticator = $customConnector->getAccessToken('code');

    expect($authenticator)
        ->toBeInstanceOf(CustomOAuthAuthenticator::class)
        ->and($authenticator->getGreeting())->toEqual('Howdy!');
});

test('you can register a global request modifier that is called on every step of the OAuth2 process', function () {
    $mockClient = new MockClient([
        GetAccessTokenRequest::class => MockResponse::make(
            ['access_token' => 'access', 'refresh_token' => 'refresh', 'expires_in' => 3600],
            200
        ),
        GetRefreshTokenRequest::class => MockResponse::make(
            ['access_token' => 'access-new', 'refresh_token' => 'refresh-new', 'expires_in' => 3600]
        ),
        GetUserRequest::class => MockResponse::make(['user' => 'Sam']),
    ]);

    $connector = new OAuth2Connector();
    $requests = [];

    $connector->oauthConfig()->setRequestModifier(function (Request $request) use (&$requests) {
        $requests[] = get_class($request);

        switch (get_class($request)) {
            case GetAccessTokenRequest::class:
                $request->query()->add('request', 'access');
                break;

            case GetRefreshTokenRequest::class:
                $request->query()->add('request', 'refresh');
                break;

            case GetUserRequest::class:
                $request->query()->add('request', 'user');
                break;
        }
    });

    $connector->withMockClient($mockClient);

    $authenticator = $connector->getAccessToken('code');

    expect($authenticator)
        ->toBeInstanceOf(AccessTokenAuthenticator::class)
        ->and($authenticator->getAccessToken())->toEqual('access')
        ->and($authenticator->getRefreshToken())->toEqual('refresh')
        ->and($authenticator->getExpiresAt())->toBeInstanceOf(DateTimeImmutable::class)
        ->and($mockClient->getLastPendingRequest()->query()->all())->toEqual(['request' => 'access']);

    $newAuthenticator = $connector->refreshAccessToken($authenticator);

    expect($newAuthenticator)
        ->toBeInstanceOf(AccessTokenAuthenticator::class)
        ->and($newAuthenticator->getAccessToken())->toEqual('access-new')
        ->and($newAuthenticator->getRefreshToken())->toEqual('refresh-new')
        ->and($newAuthenticator->getExpiresAt())->toBeInstanceOf(DateTimeImmutable::class)
        ->and($mockClient->getLastPendingRequest()->query()->all())->toEqual(['request' => 'refresh']);

    $response = $connector->getUser($newAuthenticator);

    expect($response)
        ->toBeInstanceOf(Response::class)
        ->and($mockClient->getLastPendingRequest()->query()->all())->toEqual(['request' => 'user']);

    $pendingRequest = $response->getPendingRequest();

    expect($pendingRequest->headers()->all())
        ->toEqual([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer access-new',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])
        ->and($requests)->toEqual([
            GetAccessTokenRequest::class,
            GetRefreshTokenRequest::class,
            GetUserRequest::class,
        ]);
});

test('if you attempt to use the authorization code flow without a client id it will throw an exception', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'expires_in' => 3600], 200),
    ]);

    $connector = new NoConfigAuthCodeConnector();
    $connector->withMockClient($mockClient);

    $this->expectException(OAuthConfigValidationException::class);
    $this->expectExceptionMessage('The Client ID is empty or has not been provided.');

    $connector->getAccessToken('code');
});

test('if you attempt to use the authorization code flow without a secret it will throw an exception', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'expires_in' => 3600], 200),
    ]);

    $connector = new NoConfigAuthCodeConnector();
    $connector->withMockClient($mockClient);

    $connector->oauthConfig()->setClientId('hello');

    $this->expectException(OAuthConfigValidationException::class);
    $this->expectExceptionMessage('The Client Secret is empty or has not been provided.');

    $connector->getAccessToken('code');
});

test('if you attempt to use the authorization code flow without a redirect uri it will throw an exception', function () {
    $mockClient = new MockClient([
        MockResponse::make(['access_token' => 'access', 'expires_in' => 3600], 200),
    ]);

    $connector = new NoConfigAuthCodeConnector();
    $connector->withMockClient($mockClient);

    $connector->oauthConfig()->setClientId('hello');
    $connector->oauthConfig()->setClientSecret('secret');

    $this->expectException(OAuthConfigValidationException::class);
    $this->expectExceptionMessage('The Redirect URI is empty or has not been provided.');

    $connector->getAccessToken('code');
});

test('on the connector you can overwrite all the request classes', function () {
    $mockClient = new MockClient([
        CustomAccessTokenRequest::class => MockResponse::make(
            ['access_token' => 'access', 'refresh_token' => 'refresh', 'expires_in' => 3600],
            200
        ),
        CustomRefreshTokenRequest::class => MockResponse::make(
            ['access_token' => 'access-new', 'refresh_token' => 'refresh-new', 'expires_in' => 3600]
        ),
        CustomOAuthUserRequest::class => MockResponse::make(['user' => 'Sam']),
    ]);

    $connector = new CustomRequestOAuth2Connector();
    $connector->withMockClient($mockClient);

    $accessTokenResponse = $connector->getAccessToken('code', null, null, true);

    expect($accessTokenResponse->getRequest())->toBeInstanceOf(CustomAccessTokenRequest::class);

    $refreshTokenResponse = $connector->refreshAccessToken('howdy', true);

    expect($refreshTokenResponse->getRequest())->toBeInstanceOf(CustomRefreshTokenRequest::class);

    $userResponse = $connector->getUser(new AccessTokenAuthenticator('howdy', 'partner'));

    expect($userResponse->getRequest())->toBeInstanceOf(CustomOAuthUserRequest::class);
});
