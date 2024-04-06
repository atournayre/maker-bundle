<?php
declare(strict_types=1);

namespace App\Contracts\Service;

use App\Exception\FailFast;
use App\VO\Context;

interface FailFastInterface
{
	/**
	 * Implement logic here, or remove method and interface from the class if not needed.
	 * @throws FailFast
	 */
	public function failFast($object, Context $context): void;
}
