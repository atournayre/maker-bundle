<?php
declare(strict_types=1);

namespace App\Trait;

use Webmozart\Assert\Assert;

trait IsTrait
{
	public function is(self $object): bool
	{
		Assert::propertyExists($object, 'id', 'Object must have an id property');
		return $this === $object;
	}


	public function isNot(self $object): bool
	{
		Assert::propertyExists($object, 'id', 'Object must have an id property');
		return $this !== $object;
	}
}
