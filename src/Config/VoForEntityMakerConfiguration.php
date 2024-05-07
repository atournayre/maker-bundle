<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

class VoForEntityMakerConfiguration extends MakerConfiguration
{
    /** @var array<array{fieldName: string, type: string, nullable: bool}> */
    private array $properties = [];
    private array $propertiesAllowedTypes = [];
    private string $relatedEntity = '';

    public function properties(): array
    {
        return $this->properties;
    }

    public function withProperty(string $fieldName, string $type, bool $nullable = false): self
    {
        $config = clone $this;
        $config->properties[] = [
            'fieldName' => $fieldName,
            'type' => $type,
            'nullable' => $nullable,
        ];
        return $config;
    }

    public function withProperties(array $properties): self
    {
        $config = clone $this;
        $config->properties = $properties;
        return $config;
    }

    public function propertiesAllowedTypes(): array
    {
        return $this->propertiesAllowedTypes;
    }

    public function withPropertiesAllowedTypes(array $propertiesAllowedTypes): self
    {
        $config = clone $this;
        $config->propertiesAllowedTypes = $propertiesAllowedTypes;
        return $config;
    }

    public function relatedEntity(): string
    {
        return $this->relatedEntity;
    }

    public function withRelatedEntity(string $relatedEntity): self
    {
        $config = clone $this;
        $config->relatedEntity = $relatedEntity;
        return $config;
    }
}
