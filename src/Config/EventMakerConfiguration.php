<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use function Symfony\Component\String\u;

class EventMakerConfiguration extends MakerConfiguration
{
    /** @var array<array{fieldName: string, type: string, nullable: bool}> */
    private array $properties = [];
    private array $propertiesAllowedTypes = [];

    public static function fromFqcn(string $rootDir, string $rootNamespace, string $fqcn,): static
    {
        u($fqcn)->ensureEnd('Event');

        return parent::fromFqcn($rootDir, $rootNamespace, $fqcn);
    }

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
}
