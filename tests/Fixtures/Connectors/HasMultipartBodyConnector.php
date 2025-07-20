<?php

declare(strict_types=1);

namespace Salette\Tests\Fixtures\Connectors;

use Salette\Contracts\Body\HasBody;
use Salette\Data\MultipartValue;
use Salette\Http\Connector;
use Salette\Traits\Body\HasMultipartBody;
use Salette\Traits\Plugins\AcceptsJson;

class HasMultipartBodyConnector extends Connector implements HasBody
{
    use AcceptsJson;
    use HasMultipartBody;

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
            new MultipartValue('nickname', 'Gareth', 'user.txt', ['X-Saloon' => 'Yee-haw!']),
            new MultipartValue('drink', 'Moonshine', 'moonshine.txt', ['X-My-Head' => 'Spinning!']),
        ];
    }
}
