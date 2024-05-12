<?php
declare(strict_types=1);

namespace App\VO\Null;

use App\Contracts\Security\UserInterface;
use App\Contracts\VO\ContextInterface;
use App\Contracts\VO\DateTimeInterface;
use App\VO\DateTime;

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
final class NullContext implements ContextInterface
{
	private function __construct(
		private readonly UserInterface $user,
		private readonly DateTime $createdAt,
	) {
	}


	/**
	 * @throws \Exception
	 */
	public static function create(): self
	{
		return new self(NullUser::create(), DateTime::fromInterface(new \DateTime('1970-01-01 00:00:00')));
	}


	public function user(): UserInterface
	{
		return $this->user;
	}


    /**
     * @throws \Exception
     */
    public function createdAt(): DateTimeInterface
	{
		return $this->createdAt;
	}
}
