<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO;

use Atournayre\Bundle\MakerBundle\Helper\Str;

final class PropertyDefinition
{
    private function __construct(
        public string $fieldName,
        public string $type,
        public bool   $nullable = false
    )
    {
    }

    /**
     * @param array{fieldName: string, type: string, nullable?: bool} $data
     */
    public static function fromArray(array $data, string $rootDir, string $rootNamespace): self
    {
        $type = $data['type'];
        if (self::doIsVo($type)) {
            $namespaceFromPath = Str::namespaceFromPath($type, $rootDir);
            $type = Str::prefixByRootNamespace($namespaceFromPath, $rootNamespace);
        }

        return new self(
            $data['fieldName'],
            $type,
            $data['nullable'] ?? false
        );
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
        return self::doIsVo($this->type);
    }

    private static function doIsVo(string $type): bool
    {
        return !self::doIsPrimitive($type)
            && !self::doIsDateTime($type);
    }
}
