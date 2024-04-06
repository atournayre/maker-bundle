<?php
declare(strict_types=1);

namespace App\Contracts\Service;

use App\VO\Context;

interface CommandServiceInterface
{
	public function execute($object, Context $context, ?string $service = null): void;
}
