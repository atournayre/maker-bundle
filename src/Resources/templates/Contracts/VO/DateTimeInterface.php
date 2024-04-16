<?php
declare(strict_types=1);

namespace App\Contracts\VO;

interface DateTimeInterface
{
	public function isBefore(\DateTimeInterface $datetime): bool;


	public function isAfter(\DateTimeInterface $datetime): bool;


	public function isBetween(\DateTimeInterface $datetime1, \DateTimeInterface $datetime2): bool;


	public function isBeforeOrEqual(\DateTimeInterface $datetime): bool;


	public function isAfterOrEqual(\DateTimeInterface $datetime): bool;


	public function isBetweenOrEqual(\DateTimeInterface $datetime1, \DateTimeInterface $datetime2): bool;


	public function toDateTime(): \DateTimeInterface;
}
