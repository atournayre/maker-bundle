<?php

declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO;

use Atournayre\Bundle\MakerBundle\Helper\Str;

final class AllowedTypeDefinition implements \Stringable
{
	private function __construct(
		private string $type,
	) {
	}

	public static function create(string $type, string $rootDir, string $rootNamespace): self
	{
		if (self::doIsPrimitive($type)) {
            return new self($type);
        }

        if (self::doIsDateTime($type)) {
            return new self($type);
        }

        $namespaceFromPath = Str::namespaceFromPath($type, $rootDir);
        $namespace = Str::prefixByRootNamespace($namespaceFromPath, $rootNamespace);

		return new self($namespace);
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function withType(string $type): self
	{
		$clone = clone $this;
		$clone->type = $type;
		return $clone;
	}

    public function isPrimitive(): bool
    {
        return self::doIsPrimitive($this->type);
    }

    private static function doIsPrimitive(string $type): bool
    {
        return in_array($type, ['string', 'int', 'float', 'bool', 'array']);
    }

    public function isDateTime(): bool
    {
        return self::doIsDateTime($this->type);
    }

    private static function doIsDateTime(string $type): bool
    {
        return $type === '\DateTimeInterface';
    }

    public function isVo(): bool
    {
        return !$this->isPrimitive()
            && !$this->isDateTime();
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
