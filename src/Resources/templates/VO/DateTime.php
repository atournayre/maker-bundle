<?php
declare(strict_types=1);

namespace App\VO;

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
final class DateTime
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


	function isBefore(\DateTimeInterface $datetime): bool
	{
		return $this->datetime < $datetime;
	}


	function isAfter(\DateTimeInterface $datetime): bool
	{
		return $this->datetime > $datetime;
	}


	function isBetween(\DateTimeInterface $datetime1, \DateTimeInterface $datetime2): bool
	{
		return $this->datetime > $datetime1 && $this->datetime < $datetime2;
	}


	function isBeforeOrEqual(\DateTimeInterface $datetime): bool
	{
		return $this->datetime <= $datetime;
	}


	function isAfterOrEqual(\DateTimeInterface $datetime): bool
	{
		return $this->datetime >= $datetime;
	}


	function isBetweenOrEqual(\DateTimeInterface $datetime1, \DateTimeInterface $datetime2): bool
	{
		return $this->datetime >= $datetime1 && $this->datetime <= $datetime2;
	}


	function toDateTime(): \DateTimeInterface
	{
		return $this->datetime;
	}
}
