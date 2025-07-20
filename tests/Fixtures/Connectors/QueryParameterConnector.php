<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Http\Connector;
use Salette\Traits\Plugins\AcceptsJson;

class QueryParameterConnector extends Connector
{
    use AcceptsJson;

    /**
     * Constructor
     */
    public function __construct($url = null)
    {
        if (is_null($this->url)) {
            $this->url = apiUrl();
        }
    }

    public function resolveBaseUrl(): string
    {
        return $this->url;
    }

    protected function defaultQuery(): array
    {
        return [
            'sort' => 'first_name',
        ];
    }
}
