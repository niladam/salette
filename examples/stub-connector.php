<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Salette\Helpers\ConnectorStubber;
use Salette\Helpers\Stub;

// Example 1: Generate a basic connector
echo "Generating basic connector...\n";
$result = ConnectorStubber::make('github-api', __DIR__ . '/connectors', [
    'base_url' => 'https://api.github.com',
    'namespace' => 'Examples\\Connectors'
]);

if ($result) {
    echo "✓ Basic connector generated successfully!\n";
} else {
    echo "✗ Failed to generate basic connector\n";
}

// Example 2: Generate a connector with JSON support using the facade
echo "\nGenerating JSON connector...\n";
$result = Stub::jsonConnector('stripe-api', __DIR__ . '/connectors', [
    'base_url' => 'https://api.stripe.com/v1',
    'namespace' => 'Examples\\Connectors'
]);

if ($result) {
    echo "✓ JSON connector generated successfully!\n";
} else {
    echo "✗ Failed to generate JSON connector\n";
}

// Example 3: Generate a connector with token authentication
echo "\nGenerating token auth connector...\n";
$result = Stub::tokenConnector('slack-api', __DIR__ . '/connectors', [
    'base_url' => 'https://slack.com/api',
    'namespace' => 'Examples\\Connectors'
]);

if ($result) {
    echo "✓ Token auth connector generated successfully!\n";
} else {
    echo "✗ Failed to generate token auth connector\n";
}

// Example 4: Generate a connector with basic authentication
echo "\nGenerating basic auth connector...\n";
$result = Stub::basicAuthConnector('jira-api', __DIR__ . '/connectors', [
    'base_url' => 'https://your-domain.atlassian.net/rest/api/3',
    'namespace' => 'Examples\\Connectors'
]);

if ($result) {
    echo "✓ Basic auth connector generated successfully!\n";
} else {
    echo "✗ Failed to generate basic auth connector\n";
}

// Example 5: Generate a custom connector with specific options
echo "\nGenerating custom connector...\n";
$stubber = new ConnectorStubber(__DIR__ . '/connectors', 'Examples\\Connectors');
$result = $stubber->generate('custom-api', [
    'base_url' => 'https://api.custom.com',
    'accepts_json' => true,
    'has_headers' => true,
    'has_config' => true,
    'has_auth' => true,
    'auth_type' => 'token'
]);

if ($result) {
    echo "✓ Custom connector generated successfully!\n";
} else {
    echo "✗ Failed to generate custom connector\n";
}

echo "\nAll connectors have been generated in the 'examples/connectors' directory.\n";
echo "You can now use these connectors in your Salette integration!\n"; 