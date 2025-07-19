<?php

declare(strict_types=1);

namespace Salette\Repositories;

use Salette\Traits\Body\CreatesStreamFromString;
use Stringable;

class JsonBodyRepository extends ArrayBodyRepository implements Stringable
{
    use CreatesStreamFromString;

    /**
     * JSON encoding flags
     *
     * Use a Bitmask to separate other flags. For example: JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
     */
    protected int $jsonFlags = JSON_THROW_ON_ERROR;

    /**
     * Set the JSON encoding flags
     *
     * Must be a bitmask like: ->setJsonFlags(JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
     */
    public function setJsonFlags(int $flags): JsonBodyRepository
    {
        $this->jsonFlags = $flags;

        return $this;
    }

    /**
     * Get the JSON encoding flags
     */
    public function getJsonFlags(): int
    {
        return $this->jsonFlags;
    }

    /**
     * Convert the body repository into a string.
     */
    public function __toString(): string
    {
        /** @noinspection JsonEncodingApiUsageInspection */
        $json = json_encode($this->all(), $this->getJsonFlags());

        return $json === false ? '' : $json;
    }
}
