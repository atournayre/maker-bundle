<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Atournayre\Bundle\MakerBundle\Traits\Config\EnableApiPlatformTrait;
use Atournayre\Bundle\MakerBundle\Traits\Config\PropertiesAllowedTypesTrait;
use Atournayre\Bundle\MakerBundle\Traits\Config\PropertiesAccessorsTrait;

class TraitForEntityMakerConfiguration extends MakerConfiguration
{
    use PropertiesAccessorsTrait;
    use PropertiesAllowedTypesTrait;
    use EnableApiPlatformTrait;

    private bool $isUsedByEntity = false;

    protected static function classNameSuffix(): string
    {
        return 'Trait';
    }

    public function isUsedByEntity(): bool
    {
        return $this->isUsedByEntity;
    }

    public function withIsUsedByEntity(bool $isUsedByEntity = true): self
    {
        $config = clone $this;
        $config->isUsedByEntity = $isUsedByEntity;
        return $config;
    }
}
