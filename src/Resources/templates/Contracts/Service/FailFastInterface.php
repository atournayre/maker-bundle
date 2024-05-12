<?php
declare(strict_types=1);

namespace App\Contracts\Service;

use App\Contracts\VO\ContextInterface;
use App\Exception\FailFast;

interface FailFastInterface
{
	/**
	 * Implement logic here, or remove method and interface from the class if not needed.
	 * @throws FailFast
	 */
	public function failFast($object, ContextInterface $context): void;
}
