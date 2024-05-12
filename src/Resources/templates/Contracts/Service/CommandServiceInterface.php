<?php
declare(strict_types=1);

namespace App\Contracts\Service;

use App\Contracts\VO\ContextInterface;

interface CommandServiceInterface
{
	public function execute($object, ContextInterface $context, ?string $service = null): void;
}
