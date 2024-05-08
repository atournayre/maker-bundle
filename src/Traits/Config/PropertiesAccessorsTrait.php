<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Traits\Config;

use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Traits\PropertiesTrait;

trait PropertiesAccessorsTrait
{
    use PropertiesTrait;

    /**
     * @return array|PropertyDefinition[]
     */
    public function properties(): array
    {
        return $this->properties;
    }

    /**
     * @param array|PropertyDefinition[] $properties
     */
    public function withProperties(array $properties): self
    {
        $config = clone $this;
        $config->properties = $properties;
        return $config;
    }
}
