<p align="center"><img src="/art/salette_transparent.png" alt="A legacy focus backport of Saloon"></p>

<div align="center">

# Salette

## The power of [Saloon](https://github.com/saloonphp/saloon) - for PHP 7.4

</div>

**Salette** is a port of the [Saloon](https://github.com/saloonphp/saloon) PHP package, tailored specifically for legacy applications that require PHP 7.4 compatibility.

This package exists with **permission from the original author [Sam Carré](https://github.com/Sammyjo20)** and aims to mirror and ensure the functionality and developer experience of **Saloon** as closely as possible within the limitations of PHP 7.4.

> [!TIP]
> If your application uses **PHP 8.0 or higher**, you should use the [official Saloon package](https://github.com/saloonphp/saloon) instead.


## What is Salette?

Salette helps you build clean, reusable **API integrations** and SDKs in **PHP 7.4**, in the same way you would do using Saloon.

It organizes your requests into structured classes, centralizing your API logic and making your codebase more maintainable.

```php
<?php

$forge = new ForgeConnector('api-token');

$response = $forge->send(new GetServersRequest);

$data = $response->json();
```

## Key Features

- Provides a simple, easy-to-learn, and modern way to build clean, reusable API integrations
- Built on top of Guzzle, the most popular and feature-rich HTTP client
- Works great within a team as it provides a standard everyone can follow
- Great for building your next PHP SDK or library
- Packed full of features like request recording, request concurrency, caching, data-transfer-object support, and full Laravel support.
- Framework agnostic
- Lightweight and has few dependencies.


## Credits

- [Sam Carré](https://github.com/Sammyjo20)
- [All Contributors](https://github.com/Sammyjo20/Saloon/contributors)
- [Madalin Tache](https://github.com/niladam)
- [Catalin Pruna](https://github.com/PrunaCatalin)

And a special thanks to [Caneco](https://twitter.com/caneco) for the logo ✨
