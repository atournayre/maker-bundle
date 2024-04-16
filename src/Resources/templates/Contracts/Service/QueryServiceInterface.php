<?php
declare(strict_types=1);

namespace App\Contracts\Service;

use App\Contracts\VO\ContextInterface;

interface QueryServiceInterface
{
	public function fetch($object, ContextInterface $context, ?string $service = null);
}
