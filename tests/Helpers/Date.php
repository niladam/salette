<?php

declare(strict_types=1);

namespace Salette\Tests\Helpers;

use DateInterval;
use DateTime;
use DateTimeImmutable;

final class Date
{
    protected DateTime $dateTime;

    /**
     * Constructor
     */
    public function __construct(DateTime $dateTime)
    {
        //
        $this->dateTime = $dateTime;
    }

    /**
     * Construct
     */
    public static function now()
    {
        return new self(new DateTime());
    }

    /**
     * Add seconds
     *
     * @return $this
     */
    public function addSeconds(int $seconds)
    {
        $this->dateTime->add(
            DateInterval::createFromDateString($seconds . ' seconds')
        );

        return $this;
    }

    /**
     * Subtract minutes
     *
     * @return $this
     */
    public function subMinutes(int $minutes)
    {
        $this->dateTime->sub(
            DateInterval::createFromDateString($minutes . ' minutes')
        );

        return $this;
    }

    /**
     * Get the datetime instance
     */
    public function toDateTime()
    {
        return DateTimeImmutable::createFromMutable($this->dateTime);
    }
}
