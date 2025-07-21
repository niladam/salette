<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Salette\Helpers\Stub;

echo "🚀 Salette Integration Generator\n";
echo "================================\n\n";

// Ask for integration name with default
echo "Enter the name of your integration (or press Enter for 'JsonPlaceholder'): ";
$integrationName = trim(fgets(STDIN));

if (empty($integrationName)) {
    $integrationName = 'JsonPlaceholder';
    echo "✅ Using default: JsonPlaceholder\n";
}

// Ask for custom base URL with default
echo "Enter the base URL for the API (or press Enter for 'https://jsonplaceholder.typicode.com'): ";
$baseUrl = trim(fgets(STDIN));

if (empty($baseUrl)) {
    $baseUrl = 'https://jsonplaceholder.typicode.com';
    echo "✅ Using default: https://jsonplaceholder.typicode.com\n";
}

echo "\n📁 Creating integration...\n\n";

try {
    $result = Stub::create($integrationName, $baseUrl);

    if ($result) {
        echo "✅ Integration created successfully!\n\n";
        
        echo "📁 Files created:\n";
        echo "  - Http/Integrations/{$integrationName}/{$integrationName}Connector.php\n";
        echo "  - Http/Integrations/{$integrationName}/Requests/{$integrationName}GetRequest.php\n";
        echo "  - Http/Integrations/{$integrationName}/Requests/{$integrationName}CreateRequest.php\n\n";
        
        echo "🔗 Documentation: https://docs.saloon.dev/\n\n";
        
        echo "💡 Next steps:\n";
        echo "  1. Update the connector with your API configuration\n";
        echo "  2. Modify the requests to match your API endpoints\n";
        echo "  3. Add authentication if needed\n";
        echo "  4. Start building your integration!\n\n";
        
        echo "📖 Example usage:\n";
        echo "```php\n";
        echo "use App\\Http\\Integrations\\{$integrationName}\\{$integrationName}Connector;\n";
        echo "use App\\Http\\Integrations\\{$integrationName}\\Requests\\{$integrationName}GetRequest;\n";
        echo "use App\\Http\\Integrations\\{$integrationName}\\Requests\\{$integrationName}CreateRequest;\n\n";
        echo "\$connector = new {$integrationName}Connector();\n";
        echo "\$response = \$connector->send(new {$integrationName}GetRequest());\n";
        echo "\$data = \$response->json();\n";
        echo "```\n";
        
    } else {
        echo "❌ Failed to create integration.\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Error creating integration: " . $e->getMessage() . "\n";
    exit(1);
} 