<?php
declare(strict_types=1);

namespace App\Logger;

use Psr\Log\LoggerInterface;

abstract class AbstractLogger implements LoggerInterface
{
	private ?string $logIdentifier = null;


	function __construct(
		protected LoggerInterface $logger,
	) {
	}


	protected function setLoggerIdentifier(string $identifier): void
	{
		$this->logIdentifier = $identifier;
	}


	protected function getLoggerIdentifier(): string
	{
		return $this->logIdentifier ?? static::class;
	}


	protected function prefixMessage(string $prefix, \Stringable|string $message): string
	{
		return sprintf('[%s] %s', $prefix, $message);
	}


	abstract public function emergency(\Stringable|string $message, array $context = []): void;


	abstract public function alert(\Stringable|string $message, array $context = []): void;


	abstract public function critical(\Stringable|string $message, array $context = []): void;


	abstract public function error(\Stringable|string $message, array $context = []): void;


	abstract public function warning(\Stringable|string $message, array $context = []): void;


	abstract public function notice(\Stringable|string $message, array $context = []): void;


	abstract public function info(\Stringable|string $message, array $context = []): void;


	abstract public function debug(\Stringable|string $message, array $context = []): void;


	abstract public function log($level, \Stringable|string $message, array $context = []): void;


	abstract public function exception(\Exception $exception, array $context = []): void;


	abstract public function start(array $context = []): void;


	abstract public function end(array $context = []): void;


	abstract public function success(array $context = []): void;


	abstract public function failFast(array $context = []): void;
}
