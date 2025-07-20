<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Contracts\Body\HasBody;
use Salette\Http\Connector;
use Salette\Traits\Body\HasJsonBody;
use Salette\Traits\Plugins\AcceptsJson;

class HasJsonBodyConnector extends Connector implements HasBody
{
    use AcceptsJson;
    use HasJsonBody;

    public bool $unique = false;

    /**
     * Constructor
     */
    public function __construct(?string $url = null)
    {
        $this->url = $url;
    }

    /**
     * Define the base url of the api.
     */
    public function resolveBaseUrl(): string
    {
        return $this->url ?? apiUrl();
    }

    /**
     * Define the base headers that will be applied in every request.
     *
     * @return string[]
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    protected function defaultBody(): array
    {
        return [
            'name' => 'Gareth',
            'drink' => 'Moonshine',
        ];
    }
}
