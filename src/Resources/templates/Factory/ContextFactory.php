<?php
declare(strict_types=1);

namespace App\Factory;

use App\Contracts\Security\SecurityInterface;
use App\Contracts\Security\UserInterface;
use App\Contracts\VO\ContextInterface;
use App\VO\Context;
use App\VO\Null\NullUser;
use Psr\Clock\ClockInterface;

final class ContextFactory
{
	public function __construct(
		public readonly SecurityInterface $security,
		public readonly ClockInterface $clock,
	) {
	}


	/**
	 * @throws \Exception
	 */
	public function create(UserInterface $user = null, \DateTimeInterface $dateTime = null): ContextInterface
	{
		return Context::create(
		    $user ?? $this->security->getUser() ?? NullUser::create(),
		    $dateTime ?? $this->clock->now()
		);
	}
}
