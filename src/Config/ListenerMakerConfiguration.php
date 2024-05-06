<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use function Symfony\Component\String\u;

class ListenerMakerConfiguration extends MakerConfiguration
{
    private string $eventNamespace = '';
    private array $propertiesAllowedTypes = [];

    public static function fromFqcn(string $rootDir, string $rootNamespace, string $fqcn,): static
    {
        u($fqcn)->ensureEnd('Listener');

        return parent::fromFqcn($rootDir, $rootNamespace, $fqcn);
    }

    public function eventNamespace(): string
    {
        return $this->eventNamespace;
    }

    public function withEventNamespace(string $eventNamespace): self
    {
        $config = clone $this;
        $config->eventNamespace = $eventNamespace;
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
