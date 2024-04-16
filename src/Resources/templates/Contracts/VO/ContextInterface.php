<?php
declare(strict_types=1);

namespace App\Contracts\VO;

use App\Contracts\Security\UserInterface;

interface ContextInterface
{
	public function user(): UserInterface;


	public function createdAt(): DateTimeInterface;
}
