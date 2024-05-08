<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO;

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
    public static function fromArray(array $data): self
    {
        return new self(
            $data['fieldName'],
            $data['type'],
            $data['nullable'] ?? false
        );
    }

    public function typeIsPrimitive(): bool
    {
        return !str_contains($this->type, '\\');
    }

    public function typeIsDateTimeInterface(): bool
    {
        return $this->type === '\DateTimeInterface';
    }
}
