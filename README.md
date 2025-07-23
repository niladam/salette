<p align="center"><img src="/art/salette_transparent.png" alt="A legacy focus backport of Saloon"></p>

<div align="center">

# Salette

## The power of [Saloon](https://github.com/saloonphp/saloon) - for PHP 7.4

[![Downloads](https://img.shields.io/packagist/dt/niladam/salette.svg)](https://packagist.org/packages/niladam/salette)
[![Tests](https://github.com/niladam/salette/workflows/Tests/badge.svg)](https://github.com/niladam/salette/actions?query=workflow%3ATests)
[![PHP 7.4](https://img.shields.io/badge/PHP-7.4-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

</div>

**Salette** is a port of the [Saloon](https://github.com/saloonphp/saloon) PHP package, tailored specifically for legacy applications that require PHP 7.4 compatibility.

This package exists with **permission from the original author [Sam Carré](https://github.com/Sammyjo20)** and aims to mirror and ensure the functionality and developer experience of **Saloon** as closely as possible within the limitations of PHP 7.4.

> [!TIP]
> If your application uses **PHP 8.0 or higher**, you should use the [official Saloon package](https://github.com/saloonphp/saloon) instead.


## What is Salette?

Salette helps you build clean, reusable **API integrations** and SDKs in **PHP 7.4**, in the same way you would do using Saloon.

It organizes your requests into structured classes, centralizing your API logic and making your codebase more maintainable.


## Key Features

- 🚀 **PHP 7.4 Compatible** - Built specifically for legacy applications
- 🔧 **Simple & Modern** - Easy-to-learn, clean API for building integrations
- 🏗️ **Built on Guzzle** - Leverages the most popular and feature-rich HTTP client
- 🧪 **Built-in Testing** - Mock responses, request recording, and test your integrations easily
- 🔐 **Authentication** - Support for OAuth2, Basic Auth, Token Auth, and more
- 📦 **Request/Response Handling** - Built-in support for JSON, XML, Form data, and more
- 🔄 **Advanced Features** - Request concurrency, caching (soon), Dto support
- 🎯 **Team-Friendly** - Provides a standard everyone can follow
- 🏢 **Framework Agnostic** - Vanilla PHP or any PHP framework that supports composer :)
- 📚 **Laravel Support** - Full Laravel integration and support (soon)
- ⚡ **Lightweight** - Few dependencies, fast performance


## Quick Start

### 1. Install the package

```shell
composer require niladam/salette
```

### 2. Generate a Working Example

The fastest way to get started is to generate a ready-to-use integration:

```shell
php vendor/niladam/salette/examples/create-integration.php
```

Or, in code:

```php
use Salette\Helpers\Stub;

Stub::create(); // Instantly creates a JsonPlaceholder integration you can use and modify
```

This will create:
- `app/Http/Integrations/JsonPlaceholder/JsonPlaceholderConnector.php`
- `app/Http/Integrations/JsonPlaceholder/Requests/GetPostsRequest.php`
- `app/Http/Integrations/JsonPlaceholder/Requests/CreatePostRequest.php`

You can use these classes right away, and just change the URL, endpoints, or request bodies to match your API.

### 3. Using Your Integration

```php
use App\Http\Integrations\JsonPlaceholder\JsonPlaceholderConnector;
use App\Http\Integrations\JsonPlaceholder\Requests\GetPostsRequest;
use App\Http\Integrations\JsonPlaceholder\Requests\CreatePostRequest;

$connector = new JsonPlaceholderConnector();

// GET /posts
$response = $connector->send(new GetPostsRequest());
$data = $response->json();

// POST /posts
$response = $connector->send(new CreatePostRequest());
$data = $response->json();
```

---

For advanced usage, options, and customization, see:
- **[Integration Stubbing Guide](docs/integration-stubbing.md)**
- **[Saloon Documentation](https://docs.saloon.dev/)**

## Credits

- [Sam Carré](https://github.com/Sammyjo20)
- [All Contributors](https://github.com/Sammyjo20/Saloon/contributors)
- [Madalin Tache](https://github.com/niladam)
- [Catalin Pruna](https://github.com/PrunaCatalin)
