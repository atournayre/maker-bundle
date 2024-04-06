<?php
declare(strict_types=1);

namespace App\VO;

use App\Contracts\Security\UserInterface;

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
final class Context
{
	private function __construct(
		private readonly UserInterface $user,
		private readonly DateTime $createdAt,
	) {
	}


	/**
	 * @throws \Exception
	 */
	public static function create(UserInterface $user, \DateTimeInterface $createdAt): self
	{
		return new self($user, DateTime::fromInterface($createdAt));
	}


	public function user(): UserInterface
	{
		return $this->user;
	}


	public function createdAt(): DateTime
	{
		return $this->createdAt;
	}
}
