<?php
declare(strict_types=1);

namespace App\VO\Null;

use App\Contracts\VO\DateTimeInterface;

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
final class NullDateTime implements DateTimeInterface
{
    private function __construct(
        private readonly \DateTimeInterface $datetime,
    ) {
    }


    /**
	 * @throws \Exception
	 */
	public static function create(): self
	{
        return new self(new \DateTime('1970-01-01 00:00:00'));
	}


	public function isBefore(\DateTimeInterface $datetime): bool
	{
		return false;
	}


	public function isAfter(\DateTimeInterface $datetime): bool
	{
		return false;
	}


	public function isBetween(\DateTimeInterface $datetime1, \DateTimeInterface $datetime2): bool
	{
		return false;
	}


	public function isBeforeOrEqual(\DateTimeInterface $datetime): bool
	{
		return false;
	}


	public function isAfterOrEqual(\DateTimeInterface $datetime): bool
	{
		return false;
	}


	public function isBetweenOrEqual(\DateTimeInterface $datetime1, \DateTimeInterface $datetime2): bool
	{
		return false;
	}


	public function toDateTime(): \DateTimeInterface
	{
		return $this->datetime;
	}
}
