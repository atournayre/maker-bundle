<?php
declare(strict_types=1);

namespace App\Contracts\Logger;

interface LoggerInterface extends \Psr\Log\LoggerInterface
{
	public function emergency(\Stringable|string $message, array $context = []): void;


	public function alert(\Stringable|string $message, array $context = []): void;


	public function critical(\Stringable|string $message, array $context = []): void;


	public function error(\Stringable|string $message, array $context = []): void;


	public function warning(\Stringable|string $message, array $context = []): void;


	public function notice(\Stringable|string $message, array $context = []): void;


	public function info(\Stringable|string $message, array $context = []): void;


	public function debug(\Stringable|string $message, array $context = []): void;


	public function log($level, \Stringable|string $message, array $context = []): void;


	public function exception(\Exception $exception, array $context = []): void;


	public function start(array $context = []): void;


	public function end(array $context = []): void;


	public function success(array $context = []): void;


	public function failFast(array $context = []): void;
}
