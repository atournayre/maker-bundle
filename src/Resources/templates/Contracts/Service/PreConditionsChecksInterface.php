<?php
declare(strict_types=1);

namespace App\Contracts\Service;

use App\VO\Context;

interface PreConditionsChecksInterface
{
	/**
	 * Use assertions, or remove method and interface from the class if not needed.
	 * @throws \Exception
	 */
	public function preConditionsChecks($object, Context $context): void;
}
