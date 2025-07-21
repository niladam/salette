# Integration Stubbing

Salette provides a powerful integration stubbing system that allows you to quickly generate complete API integrations with connectors and requests.

## Quick Start

### Interactive Integration Generator

The easiest way to create an integration is using the interactive script:

```bash
php vendor/niladam/salette/examples/create-integration.php
```

This will:
1. Ask for your integration name
2. Ask if authentication is needed
3. Ask for the API base URL
4. Create the complete integration structure
5. Output usage examples and documentation links

### Programmatic Generation

For programmatic generation, use the simple `Stub::create()` method:

```php
<?php

use Salette\Helpers\Stub;

// Generate a complete integration with default JSONPlaceholder API
Stub::create('github');

// Generate with custom base URL
Stub::create('stripe', 'https://api.stripe.com/v1');
```

## Generated Structure

Each integration creates the following structure:

```
app/Http/Integrations/{IntegrationName}/
├── {IntegrationName}Connector.php
└── Requests/
    ├── {IntegrationName}GetRequest.php
    └── {IntegrationName}CreateRequest.php
```

## Advanced Integration Generation

For more control over the generated integration you can use something like

```php
<?php

use Salette\Helpers\ConnectorStubber;

$stubber = new ConnectorStubber('app', 'App\\Http\\Integrations\\CustomApi');

$stubber->generateIntegration('custom-api', [
    'base_url' => 'https://api.custom.com',
    'has_auth' => true,
    'auth_type' => 'token'
]);
```

## Available Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `base_url` | string | `'https://jsonplaceholder.typicode.com'` | The base URL for the API |
| `has_auth` | bool | `false` | Whether to include authentication |
| `auth_type` | string | `'token'` | Type of authentication (`'token'` or `'basic'`) |
| `namespace` | string | `'App\\Http\\Integrations\\{IntegrationName}'` | The namespace for the integration classes |

## Example Usage

After generating an integration, you can use it like this:

```php
<?php

use App\Http\Integrations\Github\GithubConnector;
use App\Http\Integrations\Github\Requests\GithubGetRequest;
use App\Http\Integrations\Github\Requests\GithubCreateRequest;

$connector = new GithubConnector();

// Send a GET request
$response = $connector->send(new GithubGetRequest());
$data = $response->json();

// Send a POST request
$response = $connector->send(new GithubCreateRequest());
$data = $response->json();
```

## Generated Files

### Connector

The generated connector includes:
- Base URL configuration
- JSON support via `AcceptsJson` trait
- Default headers for JSON APIs
- Optional authentication setup
- Proper error handling

### GET Request

The GET request:
- Uses `Method::GET`
- Points to `/posts/1` endpoint (JSONPlaceholder)
- Ready for customization

### POST Request

The POST request:
- Uses `Method::POST`
- Implements `HasBody` interface
- Uses `HasJsonBody` trait
- Points to `/posts` endpoint
- Includes sample JSON body data

## Customization

After generation, you can customize:

1. **Update the connector** with your specific API configuration
2. **Modify request endpoints** to match your API
3. **Add authentication** if needed
4. **Customize request bodies** for POST requests
5. **Add additional requests** for other HTTP methods

## Testing

The generated integrations work with JSONPlaceholder by default, making them immediately testable:

```php
$connector = new TestIntegrationConnector();
$response = $connector->send(new TestIntegrationGetRequest());
$data = $response->json(); // Returns post data from JSONPlaceholder
``` 
