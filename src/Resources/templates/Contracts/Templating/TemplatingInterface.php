<?php
declare(strict_types=1);

namespace App\Contracts\Templating;

interface TemplatingInterface
{
	public function render(string $template, array $parameters = []): string;
}
