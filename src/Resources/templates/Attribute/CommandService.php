<?php
declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class CommandService
{
	public readonly string $serviceName;


	function __construct(string $serviceName)
	{
	}
}