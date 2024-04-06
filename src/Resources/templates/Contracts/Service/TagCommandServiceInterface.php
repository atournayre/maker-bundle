<?php
declare(strict_types=1);

namespace App\Contracts\Service;

use App\VO\Context;

interface TagCommandServiceInterface
{
	/**
	 * @throws \Exception
	 */
	public function execute($object, Context $context): void;
}
