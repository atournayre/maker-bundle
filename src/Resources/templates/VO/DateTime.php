<?php

/**
 * ONLY
 * - primitive types : string, int, float, bool, array, \DateTimeInterface or VO
 *
 * MUST
 * - check validity of the data on creation
 * - be immutable
 * - be final
 *
 * SHOULD
 * - have a named constructor
 * - have withers
 * - have logic
 *
 * MUST NOT
 * - have setters
 *
 * @object-type VO
 */

declare(strict_types=1);

namespace App\VO;

use App\Contracts\VO\DateTimeInterface;
use Carbon\Carbon;

class DateTime implements DateTimeInterface
{
    private function __construct(
        private readonly \DateTimeInterface $datetime,
    ) {
    }

    /**
     * @throws \Exception
     */
    public static function fromInterface(\DateTimeInterface $datetime): self
    {
        return new self($datetime);
    }

    public function isAM(): bool
    {
        $noon = Carbon::parse($this->datetime)->setTime(12, 0);
        return $this->toCarbon()->lt($noon);
    }

    public function isAfter(\DateTimeInterface $datetime): bool
    {
        return $this->toCarbon()->gt($datetime);
    }

    public function isAfterOrEqual(\DateTimeInterface $datetime): bool
    {
        return $this->toCarbon()->gte($datetime);
    }

    public function isBefore(\DateTimeInterface $datetime): bool
    {
        return $this->toCarbon()->lt($datetime);
    }

    public function isBeforeOrEqual(\DateTimeInterface $datetime): bool
    {
        return $this->toCarbon()->lte($datetime);
    }

    public function isBetween(\DateTimeInterface $datetime1, \DateTimeInterface $datetime2): bool
    {
        return $this->toCarbon()->between($datetime1, $datetime2, false);
    }

    public function isBetweenOrEqual(\DateTimeInterface $datetime1, \DateTimeInterface $datetime2): bool
    {
        return $this->toCarbon()->between($datetime1, $datetime2);
    }

    public function isNotBetween(\DateTimeInterface $datetime1, \DateTimeInterface $datetime2): bool
    {
        return !$this->isBetween($datetime1, $datetime2);
    }

    public function isPM(): bool
    {
        return !$this->isAM();
    }

    public function isSame(\DateTimeInterface $datetime): bool
    {
        return $this->toCarbon()->eq($datetime);
    }

    public function isSameOrAfter(\DateTimeInterface $datetime): bool
    {
        return $this->toCarbon()->gte($datetime);
    }

    public function isSameOrBefore(\DateTimeInterface $datetime): bool
    {
        return $this->toCarbon()->lte($datetime);
    }

    public function isSameOrBetween(\DateTimeInterface $datetime1, \DateTimeInterface $datetime2): bool
    {
        return $this->toCarbon()->between($datetime1, $datetime2);
    }

    public function isWeekday(): bool
    {
        return !$this->isWeekend();
    }

    public function isWeekend(): bool
    {
        return $this->toCarbon()->isWeekend();
    }

    public function toCarbon(): Carbon
    {
        return Carbon::parse($this->datetime);
    }

    public function toDateTime(): \DateTimeInterface
    {
        return $this->datetime;
    }
}
