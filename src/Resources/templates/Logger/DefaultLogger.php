<?php
declare(strict_types=1);

namespace App\Logger;

use App\Contracts\Logger\LoggerInterface;

final class DefaultLogger extends AbstractLogger implements LoggerInterface
{
	public function exception(\Exception $exception, array $context = []): void
	{
		$context['exception'] = $exception;
		$this->logger->error($exception->getMessage(), $context);
	}


	public function error(\Stringable|string $message, array $context = []): void
	{
		$this->logger->error($this->prefixMessage($this->getLoggerIdentifier(), $message), $context);
	}


	public function emergency(\Stringable|string $message, array $context = []): void
	{
		$this->logger->emergency($this->prefixMessage($this->getLoggerIdentifier(), $message), $context);
	}


	public function alert(\Stringable|string $message, array $context = []): void
	{
		$this->logger->alert($this->prefixMessage($this->getLoggerIdentifier(), $message), $context);
	}


	public function critical(\Stringable|string $message, array $context = []): void
	{
		$this->logger->critical($this->prefixMessage($this->getLoggerIdentifier(), $message), $context);
	}


	public function warning(\Stringable|string $message, array $context = []): void
	{
		$this->logger->warning($this->prefixMessage($this->getLoggerIdentifier(), $message), $context);
	}


	public function notice(\Stringable|string $message, array $context = []): void
	{
		$this->logger->notice($this->prefixMessage($this->getLoggerIdentifier(), $message), $context);
	}


	public function info(\Stringable|string $message, array $context = []): void
	{
		$this->logger->info($this->prefixMessage($this->getLoggerIdentifier(), $message), $context);
	}


	public function debug(\Stringable|string $message, array $context = []): void
	{
		$this->logger->debug($this->prefixMessage($this->getLoggerIdentifier(), $message), $context);
	}


	public function log($level, \Stringable|string $message, array $context = []): void
	{
		$this->logger->log($level, $this->prefixMessage($this->getLoggerIdentifier(), $message), $context);
	}


	public function start(array $context = []): void
	{
		$this->logger->info($this->prefixMessage($this->getLoggerIdentifier(), 'start'), $context);
	}


	public function end(array $context = []): void
	{
		$this->logger->info($this->prefixMessage($this->getLoggerIdentifier(), 'end'), $context);
	}


	public function success(array $context = []): void
	{
		$this->logger->info($this->prefixMessage($this->getLoggerIdentifier(), 'success'), $context);
	}


	public function failFast(array $context = []): void
	{
		$this->logger->info($this->prefixMessage($this->getLoggerIdentifier(), 'fail fast'), $context);
	}
}
