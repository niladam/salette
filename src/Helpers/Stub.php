<?php

declare(strict_types=1);

namespace Salette\Helpers;

class Stub
{
    /**
     * Generate a complete integration with connector and requests.
     *
     * If called with no arguments, generates a JsonPlaceholder integration with
     * GetPostsRequest (GET /posts) and CreatePostRequest (POST /posts).
     */
    public static function create(?string $integrationName = null, ?string $baseUrl = null): bool
    {
        // If no arguments, generate the JsonPlaceholder example
        if ($integrationName === null && $baseUrl === null) {
            $integrationName = 'JsonPlaceholder';
            $baseUrl = 'https://jsonplaceholder.typicode.com';
            $baseDirectory = self::getBaseDirectory();

            return self::generateJsonPlaceholderExample($baseDirectory, $integrationName, $baseUrl);
        }

        // Otherwise, normal integration
        $integrationName = $integrationName ?? 'Integration';
        $baseUrl = $baseUrl ?? 'https://jsonplaceholder.typicode.com';
        $baseDirectory = self::getBaseDirectory();
        $namespace = 'App\\Http\\Integrations\\' . $integrationName;

        $stubber = new ConnectorStubber($baseDirectory, $namespace);

        return $stubber->generateIntegration(
            $integrationName, [
                'base_url' => $baseUrl,
            ]
        );
    }

    /**
     * Generate the default JsonPlaceholder example integration.
     */
    private static function generateJsonPlaceholderExample(string $baseDirectory, string $integrationName, string $baseUrl): bool
    {
        $namespace = 'App\\Http\\Integrations\\' . $integrationName;
        $integrationPath = $baseDirectory . '/Http/Integrations/' . $integrationName;
        $requestsPath = $integrationPath . '/Requests';

        // Create directories
        if (! is_dir($requestsPath)) {
            mkdir($requestsPath, 0755, true);
        }

        // Connector
        $connectorContent = "<?php\n\n" .
            "declare(strict_types=1);\n\n" .
            "namespace {$namespace};\n\n" .
            "use Salette\\Http\\Connector;\n" .
            "use Salette\\Traits\\Plugins\\AcceptsJson;\n\n" .
            "class {$integrationName}Connector extends Connector\n" .
            "{\n" .
            "    use AcceptsJson;\n\n" .
            "    public function resolveBaseUrl(): string\n" .
            "    {\n" .
            "        return '{$baseUrl}';\n" .
            "    }\n\n" .
            "    protected function defaultHeaders(): array\n" .
            "    {\n" .
            "        return [\n" .
            "            'Accept' => 'application/json',\n" .
            "            'Content-Type' => 'application/json',\n" .
            "        ];\n" .
            "    }\n" .
            "}\n";
        file_put_contents($integrationPath . "/{$integrationName}Connector.php", $connectorContent);

        // GetPostsRequest
        $getRequestContent = "<?php\n\n" .
            "declare(strict_types=1);\n\n" .
            "namespace {$namespace}\\Requests;\n\n" .
            "use Salette\\Enums\\Method;\n" .
            "use Salette\\Requests\\Request;\n\n" .
            "class GetPostsRequest extends Request\n" .
            "{\n" .
            "    public const METHOD = Method::GET;\n\n" .
            "    public function resolveEndpoint(): string\n" .
            "    {\n" .
            "        return '/posts';\n" .
            "    }\n" .
            "}\n";
        file_put_contents($requestsPath . '/GetPostsRequest.php', $getRequestContent);

        // CreatePostRequest
        $postRequestContent = "<?php\n\n" .
            "declare(strict_types=1);\n\n" .
            "namespace {$namespace}\\Requests;\n\n" .
            "use Salette\\Enums\\Method;\n" .
            "use Salette\\Requests\\Request;\n" .
            "use Salette\\Traits\\Body\\HasJsonBody;\n" .
            "use Salette\\Contracts\\Body\\HasBody;\n\n" .
            "class CreatePostRequest extends Request implements HasBody\n" .
            "{\n" .
            "    use HasJsonBody;\n\n" .
            "    public const METHOD = Method::POST;\n\n" .
            "    public function resolveEndpoint(): string\n" .
            "    {\n" .
            "        return '/posts';\n" .
            "    }\n\n" .
            "    protected function defaultBody(): array\n" .
            "    {\n" .
            "        return [\n" .
            "            'title' => 'Hello World',\n" .
            "            'body' => 'This is a sample post.',\n" .
            "            'userId' => 1,\n" .
            "        ];\n" .
            "    }\n" .
            "}\n";
        file_put_contents($requestsPath . '/CreatePostRequest.php', $postRequestContent);

        return true;
    }

    /**
     * Determine the base directory for the project.
     */
    private static function getBaseDirectory(): string
    {
        $possiblePaths = [
            getcwd(),
            dirname(__DIR__, 2),
            dirname(__DIR__, 2),
        ];

        foreach ($possiblePaths as $path) {
            if (is_dir($path) && (file_exists($path . '/composer.json') || file_exists($path . '/vendor'))) {
                // If there's an app folder, use it
                if (is_dir($path . '/app')) {
                    return $path . '/app';
                }

                return $path;
            }
        }

        return getcwd();
    }
}
