<?php
declare(strict_types=1);

namespace App\Service\Security;

use App\Contracts\Security\SecurityInterface;
use App\Contracts\Security\UserInterface;
use App\VO\Null\NullUser;
use Symfony\Bundle\SecurityBundle\Security;

final class SymfonySecurityService implements SecurityInterface
{
	public function __construct(
		private readonly Security $security,
	) {
	}


	public function getUser(): UserInterface
	{
		/** @var UserInterface $user */
		$user = $this->security->getUser();
		return $user ?? NullUser::create();
	}
}
