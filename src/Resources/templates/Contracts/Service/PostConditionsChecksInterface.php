<?php
declare(strict_types=1);

namespace App\Contracts\Service;

use App\VO\Context;

interface PostConditionsChecksInterface
{
	/**
	 * Use assertions, or remove method and interface from the class if not needed.
	 * @throws \Exception
	 */
	public function postConditionsChecks($object, Context $context): void;
}
