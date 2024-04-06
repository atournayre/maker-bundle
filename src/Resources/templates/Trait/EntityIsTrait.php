<?php
declare(strict_types=1);

namespace App\Trait;

use Webmozart\Assert\Assert;

trait EntityIsTrait
{
	public function is(self $entity): bool
	{
		Assert::propertyExists($entity, 'id', 'Entity must have an id property');
		return $this->id === $entity->id;
	}


	public function isNot(self $entity): bool
	{
		Assert::propertyExists($entity, 'id', 'Entity must have an id property');
		return $this->id !== $entity->id;
	}
}
