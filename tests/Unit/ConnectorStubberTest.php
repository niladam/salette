<?php

declare(strict_types=1);

use Salette\Helpers\ConnectorStubber;
use Salette\Helpers\Stub;

beforeEach(function () {
    $this->testDirectory = sys_get_temp_dir() . '/salette_test_integrations';

    if (is_dir($this->testDirectory)) {
        removeDirectory($this->testDirectory);
    }
});

afterEach(function () {
    if (is_dir($this->testDirectory)) {
        removeDirectory($this->testDirectory);
    }
});

test('can generate complete integration', function () {
    $stubber = new ConnectorStubber($this->testDirectory, 'Test\\Http\\Integrations\\TestApi');

    expect($stubber->generateIntegration('test-api'))->toBeTrue()
        ->and($this->testDirectory . '/Http/Integrations/TestApi/TestApiConnector.php')->toBeFile()
        ->and($this->testDirectory . '/Http/Integrations/TestApi/Requests')->toBeDirectory()
        ->and($this->testDirectory . '/Http/Integrations/TestApi/Requests/TestApiGetRequest.php')->toBeFile()
        ->and($this->testDirectory . '/Http/Integrations/TestApi/Requests/TestApiCreateRequest.php')->toBeFile();

    $connectorContent = file_get_contents($this->testDirectory . '/Http/Integrations/TestApi/TestApiConnector.php');

    expect($connectorContent)
        ->toContain('class TestApiConnector extends Connector')
        ->and($connectorContent)->toContain('namespace Test\\Http\\Integrations\\TestApi')
        ->and($connectorContent)->toContain('use Salette\\Traits\\Plugins\\AcceptsJson')
        ->and($connectorContent)->toContain("return 'https://jsonplaceholder.typicode.com'");

    $getRequestContent = file_get_contents($this->testDirectory . '/Http/Integrations/TestApi/Requests/TestApiGetRequest.php');

    expect($getRequestContent)
        ->toContain('class TestApiGetRequest extends Request')
        ->and($getRequestContent)->toContain('public const METHOD = Method::GET')
        ->and($getRequestContent)->toContain("return '/posts/1'");

    $postRequestContent = file_get_contents($this->testDirectory . '/Http/Integrations/TestApi/Requests/TestApiCreateRequest.php');

    expect($postRequestContent)
        ->toContain('class TestApiCreateRequest extends Request implements HasBody')
        ->and($postRequestContent)->toContain('public const METHOD = Method::POST')
        ->and($postRequestContent)->toContain("return '/posts'")
        ->and($postRequestContent)->toContain('use HasJsonBody');
});

test('can generate integration with custom options', function () {
    $stubber = new ConnectorStubber($this->testDirectory, 'Test\\Http\\Integrations\\CustomApi');

    expect($stubber->generateIntegration('custom-api', [
        'base_url' => 'https://api.custom.com',
        'has_auth' => true,
        'auth_type' => 'token'
    ]))->toBeTrue();

    $connectorPath = $this->testDirectory . '/Http/Integrations/CustomApi/CustomApiConnector.php';

    expect($connectorPath)->toBeFile();

    $content = file_get_contents($connectorPath);

    expect($content)
        ->toContain("return 'https://api.custom.com'")
        ->and($content)->toContain('use Salette\\Auth\\TokenAuthenticator')
        ->and($content)->toContain('return new TokenAuthenticator');
});

test('can generate integration with authentication', function () {
    expect(ConnectorStubber::makeWithAuth('auth-api', $this->testDirectory, [
        'namespace' => 'Test\\Http\\Integrations\\AuthApi'
    ]))->toBeTrue();

    $connectorPath = $this->testDirectory . '/Http/Integrations/AuthApi/AuthApiConnector.php';

    expect($connectorPath)->toBeFile();

    $content = file_get_contents($connectorPath);

    expect($content)
        ->toContain('use Salette\\Auth\\TokenAuthenticator')
        ->and($content)->toContain('return new TokenAuthenticator');
});

test('formats class name correctly', function () {
    $stubber = new ConnectorStubber($this->testDirectory);

    expect($stubber->formatClassName('test-api'))->toBe('TestApi')
        ->and($stubber->formatClassName('my_api'))->toBe('MyApi')
        ->and($stubber->formatClassName('custom'))->toBe('Custom');
});

test('stub create method returns true when called with no arguments', function () {
    expect(Stub::create())->toBeTrue();
});
