<?php
declare(strict_types=1);

namespace App\Contracts\Service;

use App\Contracts\VO\ContextInterface;

interface TagQueryServiceInterface
{
	/**
	 * @throws \Exception
	 */
	public function fetch($object, ContextInterface $context);
}
