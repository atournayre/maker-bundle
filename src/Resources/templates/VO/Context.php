<?php
declare(strict_types=1);

namespace App\VO;

use App\Contracts\Logger\LoggableInterface;
use App\Contracts\Security\UserInterface;
use App\Contracts\VO\ContextInterface;
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
final class Context implements ContextInterface, LoggableInterface
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


	public function createdAt(): DateTimeInterface
	{
		return $this->createdAt;
	}

    public function toLog(): array
    {
        return [
            'user' => $this->user->identifier(),
            'createdAt' => $this->createdAt->toDateTime()->format('Y-m-d H:i:s'),
        ];
    }
}
