<?php
declare(strict_types=1);

namespace App\Contracts\Routing;

interface RoutingInterface
{
	/** Generates an absolute URL, e.g. "http://example.com/dir/file". */
	public const ABSOLUTE_URL = 0;

	/** Generates an absolute path, e.g. "/dir/file". */
	public const ABSOLUTE_PATH = 1;

	/** Generates a relative path based on the current request path, e.g. "../parent-file". */
	public const RELATIVE_PATH = 2;

	/**
	 * Generates a network path, e.g. "//example.com/dir/file".
	 * Such reference reuses the current scheme but specifies the host.
	 */
	public const NETWORK_PATH = 3;

	public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string;
}
