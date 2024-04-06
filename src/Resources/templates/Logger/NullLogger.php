<?php
declare(strict_types=1);

namespace App\Logger;

use App\Contracts\Logger\LoggerInterface;

final class NullLogger extends AbstractLogger implements LoggerInterface
{
	public function exception(\Exception $exception, array $context = []): void
	{
		// Do nothing
	}


	public function error(\Stringable|string $message, array $context = []): void
	{
		// Do nothing
	}


	public function emergency(\Stringable|string $message, array $context = []): void
	{
		// Do nothing
	}


	public function alert(\Stringable|string $message, array $context = []): void
	{
		// Do nothing
	}


	public function critical(\Stringable|string $message, array $context = []): void
	{
		// Do nothing
	}


	public function warning(\Stringable|string $message, array $context = []): void
	{
		// Do nothing
	}


	public function notice(\Stringable|string $message, array $context = []): void
	{
		// Do nothing
	}


	public function info(\Stringable|string $message, array $context = []): void
	{
		// Do nothing
	}


	public function debug(\Stringable|string $message, array $context = []): void
	{
		// Do nothing
	}


	public function log($level, \Stringable|string $message, array $context = []): void
	{
		// Do nothing
	}


	public function start(array $context = []): void
	{
		// Do nothing
	}


	public function end(array $context = []): void
	{
		// Do nothing
	}


	public function success(array $context = []): void
	{
		// Do nothing
	}


	public function failFast(array $context = []): void
	{
		// Do nothing
	}
}
