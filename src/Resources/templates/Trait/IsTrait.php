<?php
declare(strict_types=1);

namespace App\Trait;

trait IsTrait
{
	public function is(self $object): bool
	{
		return $this === $object;
	}


	public function isNot(self $object): bool
	{
		return !$this->is($object);
	}
}
