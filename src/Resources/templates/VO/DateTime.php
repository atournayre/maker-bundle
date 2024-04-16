<?php
declare(strict_types=1);

namespace App\VO;

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


	public function isBefore(\DateTimeInterface $datetime): bool
	{
		return $this->datetime < $datetime;
	}


	public function isAfter(\DateTimeInterface $datetime): bool
	{
		return $this->datetime > $datetime;
	}


	public function isBetween(\DateTimeInterface $datetime1, \DateTimeInterface $datetime2): bool
	{
		return $this->datetime > $datetime1 && $this->datetime < $datetime2;
	}


	public function isBeforeOrEqual(\DateTimeInterface $datetime): bool
	{
		return $this->datetime <= $datetime;
	}


	public function isAfterOrEqual(\DateTimeInterface $datetime): bool
	{
		return $this->datetime >= $datetime;
	}


	public function isBetweenOrEqual(\DateTimeInterface $datetime1, \DateTimeInterface $datetime2): bool
	{
		return $this->datetime >= $datetime1 && $this->datetime <= $datetime2;
	}


	public function toDateTime(): \DateTimeInterface
	{
		return $this->datetime;
	}
}
