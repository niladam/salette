<?php

declare(strict_types=1);

namespace Salette\Helpers;

use Salette\Exceptions\UnableToCreateDirectoryException;
use Salette\Exceptions\UnableToCreateFileException;

/**
 * @phpstan-type StubOptions array{
 *   base_url?: string,
 *   has_auth?: bool,
 *   auth_type?: 'token'|'basic'
 * }
 */
class ConnectorStubber
{
    /**
     * The base directory where integrations will be created.
     */
    protected string $baseDirectory;

    /**
     * The namespace for the integrations.
     */
    protected string $namespace;

    /**
     * Create a new connector stubber instance.
     */
    public function __construct(string $baseDirectory, string $namespace = 'App\\Http\\Integrations')
    {
        $this->baseDirectory = rtrim($baseDirectory, '/');
        $this->namespace = trim($namespace, '\\');
    }

    /**
     * Generate a complete integration with connector and requests.
     *
     * @param  StubOptions  $options
     *
     * @throws UnableToCreateDirectoryException
     * @throws UnableToCreateFileException
     */
    public function generateIntegration(string $integrationName, array $options = []): bool
    {
        $integrationPath = $this->baseDirectory . '/Http/Integrations/' . $this->formatClassName($integrationName);

        // Create integration directory
        if (! is_dir($integrationPath)) {
            if (! mkdir($integrationPath, 0755, true)) {
                throw new UnableToCreateDirectoryException($integrationPath);
            }
        }

        // Generate connector
        $this->generateConnector($integrationName, $integrationPath, $options);

        // Generate requests
        $this->generateRequests($integrationName, $integrationPath, $options);

        return true;
    }

    /**
     * Generate a connector stub.
     *
     * @param  StubOptions  $options
     *
     * @throws UnableToCreateFileException
     */
    protected function generateConnector(string $name, string $integrationPath, array $options = []): bool
    {
        $className = $this->formatClassName($name) . 'Connector';
        $fileName = $className . '.php';
        $filePath = $integrationPath . '/' . $fileName;

        // Generate the connector content
        $content = $this->generateConnectorContent($className, $options);

        // Write the file
        if (file_put_contents($filePath, $content) === false) {
            throw new UnableToCreateFileException($filePath);
        }

        return true;
    }

    /**
     * Generate requests for the integration.
     *
     * @param  StubOptions  $options
     *
     * @throws UnableToCreateDirectoryException
     * @throws UnableToCreateFileException
     */
    protected function generateRequests(string $name, string $integrationPath, array $options = []): bool
    {
        $requestsPath = $integrationPath . '/Requests';

        // Create requests directory
        if (! is_dir($requestsPath)) {
            if (! mkdir($requestsPath, 0755, true)) {
                throw new UnableToCreateDirectoryException($requestsPath);
            }
        }

        // Generate GET request
        $this->generateGetRequest($name, $requestsPath, $options);

        // Generate POST request
        $this->generatePostRequest($name, $requestsPath, $options);

        return true;
    }

    /**
     * Generate the connector class content.
     *
     * @param  StubOptions  $options
     */
    protected function generateConnectorContent(string $className, array $options): string
    {
        $baseUrl = $options['base_url'] ?? 'https://jsonplaceholder.typicode.com';
        $hasAuth = $options['has_auth'] ?? false;
        $authType = $options['auth_type'] ?? 'token';

        $content = "<?php\n\n";
        $content .= "declare(strict_types=1);\n\n";
        $content .= "namespace {$this->namespace};\n\n";
        $content .= "use Salette\\Http\\Connector;\n";
        $content .= "use Salette\\Traits\\Plugins\\AcceptsJson;\n";

        if ($hasAuth) {
            $content .= "use Salette\\Contracts\\Authenticator;\n";
            if ($authType === 'token') {
                $content .= "use Salette\\Auth\\TokenAuthenticator;\n";
            } elseif ($authType === 'basic') {
                $content .= "use Salette\\Auth\\BasicAuthenticator;\n";
            }
        }

        $content .= "\n";
        $content .= "class {$className} extends Connector\n";
        $content .= "{\n";
        $content .= "    use AcceptsJson;\n\n";

        $content .= "    /**\n";
        $content .= "     * Define the base URL of the API.\n";
        $content .= "     */\n";
        $content .= "    public function resolveBaseUrl(): string\n";
        $content .= "    {\n";
        $content .= "        return '{$baseUrl}';\n";
        $content .= "    }\n";

        $content .= "\n";
        $content .= "    /**\n";
        $content .= "     * Define the base headers that will be applied in every request.\n";
        $content .= "     *\n";
        $content .= "     * @return string[]\n";
        $content .= "     */\n";
        $content .= "    protected function defaultHeaders(): array\n";
        $content .= "    {\n";
        $content .= "        return [\n";
        $content .= "            'Accept' => 'application/json',\n";
        $content .= "            'Content-Type' => 'application/json',\n";
        $content .= "        ];\n";
        $content .= "    }\n";

        if ($hasAuth) {
            $content .= "\n";
            $content .= "    /**\n";
            $content .= "     * Provide default authentication.\n";
            $content .= "     */\n";
            $content .= "    public function defaultAuth(): ?Authenticator\n";
            $content .= "    {\n";
            if ($authType === 'token') {
                $content .= "        return new TokenAuthenticator('your-api-token-here');\n";
            } elseif ($authType === 'basic') {
                $content .= "        return new BasicAuthenticator('username', 'password');\n";
            }
            $content .= "    }\n";
        }

        $content .= "}\n";

        return $content;
    }

    /**
     * Generate a GET request.
     *
     * @param  StubOptions  $options
     *
     * @throws UnableToCreateFileException
     */
    protected function generateGetRequest(string $name, string $requestsPath, array $options = []): bool
    {
        $className = $this->formatClassName($name) . 'GetRequest';
        $fileName = $className . '.php';
        $filePath = $requestsPath . '/' . $fileName;

        $content = "<?php\n\n";
        $content .= "declare(strict_types=1);\n\n";
        $content .= "namespace {$this->namespace}\\Requests;\n\n";
        $content .= "use Salette\\Enums\\Method;\n";
        $content .= "use Salette\\Requests\\Request;\n\n";
        $content .= "class {$className} extends Request\n";
        $content .= "{\n";
        $content .= "    public const METHOD = Method::GET;\n\n";
        $content .= "    /**\n";
        $content .= "     * Define the endpoint for the request.\n";
        $content .= "     */\n";
        $content .= "    public function resolveEndpoint(): string\n";
        $content .= "    {\n";
        $content .= "        return '/posts/1';\n";
        $content .= "    }\n";
        $content .= "}\n";

        if (file_put_contents($filePath, $content) === false) {
            throw new UnableToCreateFileException($filePath);
        }

        return true;
    }

    /**
     * Generate a POST request.
     *
     * @param  StubOptions  $options
     *
     * @throws UnableToCreateFileException
     */
    protected function generatePostRequest(string $name, string $requestsPath, array $options = []): bool
    {
        $className = $this->formatClassName($name) . 'CreateRequest';
        $fileName = $className . '.php';
        $filePath = $requestsPath . '/' . $fileName;

        $content = "<?php\n\n";
        $content .= "declare(strict_types=1);\n\n";
        $content .= "namespace {$this->namespace}\\Requests;\n\n";
        $content .= "use Salette\\Enums\\Method;\n";
        $content .= "use Salette\\Requests\\Request;\n";
        $content .= "use Salette\\Traits\\Body\\HasJsonBody;\n";
        $content .= "use Salette\\Contracts\\Body\\HasBody;\n\n";
        $content .= "class {$className} extends Request implements HasBody\n";
        $content .= "{\n";
        $content .= "    use HasJsonBody;\n\n";
        $content .= "    public const METHOD = Method::POST;\n\n";
        $content .= "    /**\n";
        $content .= "     * Define the endpoint for the request.\n";
        $content .= "     */\n";
        $content .= "    public function resolveEndpoint(): string\n";
        $content .= "    {\n";
        $content .= "        return '/posts';\n";
        $content .= "    }\n\n";
        $content .= "    /**\n";
        $content .= "     * Define the default body for the request.\n";
        $content .= "     *\n";
        $content .= "     * @return array<string, mixed>\n";
        $content .= "     */\n";
        $content .= "    protected function defaultBody(): array\n";
        $content .= "    {\n";
        $content .= "        return [\n";
        $content .= "            'title' => 'Sample Post',\n";
        $content .= "            'body' => 'This is a sample post created via Salette',\n";
        $content .= "            'userId' => 1,\n";
        $content .= "        ];\n";
        $content .= "    }\n";
        $content .= "}\n";

        if (file_put_contents($filePath, $content) === false) {
            throw new UnableToCreateFileException($filePath);
        }

        return true;
    }

    /**
     * Format the class name from the integration name.
     */
    public function formatClassName(string $name): string
    {
        $name = str_replace(['-', '_'], ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '', $name);

        return $name;
    }

    /**
     * Generate a complete integration with connector and requests.
     *
     * @param  StubOptions  $options
     *
     * @throws UnableToCreateDirectoryException
     */
    public static function make(string $integrationName, string $baseDirectory, array $options = []): bool
    {
        $stubber = new self($baseDirectory);

        return $stubber->generateIntegration($integrationName, $options);
    }

    /**
     * Generate a complete integration with authentication.
     *
     * @param  StubOptions  $options
     *
     * @throws UnableToCreateDirectoryException
     */
    public static function makeWithAuth(string $integrationName, string $baseDirectory, array $options = []): bool
    {
        $options['has_auth'] = true;

        return self::make($integrationName, $baseDirectory, $options);
    }
}
