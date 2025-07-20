<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Connector;
use Salette\Traits\Plugins\AcceptsJson;

class QueryParameterConnector extends Connector
{
    use AcceptsJson;

    /**
     * Base URL
     */
    protected string $baseUrl = '';

    /**
     * Constructor
     */
    public function __construct($url = null)
    {
        if (is_null($url)) {
            $url = apiUrl();
        }
        $this->baseUrl = $url;
    }

    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function defaultQuery(): array
    {
        return [
            'sort' => 'first_name',
        ];
    }
}
