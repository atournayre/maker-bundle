<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Traits\Config;

trait PropertiesAllowedTypesTrait
{
    /**
     * @var array<string>
     */
    private array $propertiesAllowedTypes = [];

    /**
     * @return array<string>
     */
    public function propertiesAllowedTypes(): array
    {
        return $this->propertiesAllowedTypes;
    }

    /**
     * @param array<string> $propertiesAllowedTypes
     */
    public function withPropertiesAllowedTypes(array $propertiesAllowedTypes): self
    {
        $config = clone $this;
        $config->propertiesAllowedTypes = $propertiesAllowedTypes;
        return $config;
    }
}
