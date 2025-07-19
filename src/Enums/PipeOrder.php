<?php

declare(strict_types=1);

namespace Salette\Enums;

use InvalidArgumentException;
use Salette\Contracts\Stringable;

/**
 * PHP 7.4 compatible enumeration for PipeOrder
 */
final class PipeOrder implements Stringable
{
    /**
     * Run the pipe first
     */
    public const FIRST = 'first';

    /**
     * Run the pipe last
     */
    public const LAST = 'last';

    private string $value;

    /**
     * Allowed values
     *
     * @var string[]
     */
    private static array $allowed = [
        self::FIRST,
        self::LAST,
    ];

    /**
     * Private constructor to prevent direct instantiation.
     *
     * @throws InvalidArgumentException
     */
    private function __construct(string $value)
    {
        if (! in_array($value, self::$allowed, true)) {
            throw new InvalidArgumentException("Invalid PipeOrder value: {$value}");
        }

        $this->value = $value;
    }

    /**
     * Get the string value of the enum.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Create instance from string value.
     */
    public static function from(string $value): self
    {
        return new self($value);
    }

    /**
     * First case
     */
    public static function first(): self
    {
        return new self(self::FIRST);
    }

    /**
     * Last case
     */
    public static function last(): self
    {
        return new self(self::LAST);
    }

    /**
     * Get all possible values.
     *
     * @return string[]
     */
    public static function values(): array
    {
        return self::$allowed;
    }

    /**
     * Cast to string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
