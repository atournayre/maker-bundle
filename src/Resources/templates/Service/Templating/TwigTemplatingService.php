<?php
declare(strict_types=1);

namespace App\Service\Templating;

use App\Contracts\Templating\TemplatingInterface;
use Twig\Environment;

final class TwigTemplatingService implements TemplatingInterface
{
	function __construct(
		private readonly Environment $twigEnvironment,
	) {
	}


	public function render(string $template, array $parameters = []): string
	{
		return $this->twigEnvironment->render($template, $parameters);
	}
}
