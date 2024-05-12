<?php
declare(strict_types=1);

namespace App\Contracts\Service;

use App\Contracts\VO\ContextInterface;

interface TagCommandServiceInterface
{
	/**
	 * @throws \Exception
	 */
	public function execute($object, ContextInterface $context): void;
}
