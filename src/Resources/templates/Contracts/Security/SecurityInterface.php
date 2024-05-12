<?php
declare(strict_types=1);

namespace App\Contracts\Security;

interface SecurityInterface
{
	public function getUser(): ?UserInterface;
}
