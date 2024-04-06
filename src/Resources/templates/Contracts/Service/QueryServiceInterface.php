<?php
declare(strict_types=1);

namespace App\Contracts\Service;

use App\VO\Context;

interface QueryServiceInterface
{
	public function fetch($object, Context $context, ?string $service = null);
}
