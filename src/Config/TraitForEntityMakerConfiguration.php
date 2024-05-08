<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Atournayre\Bundle\MakerBundle\Traits\Config\EnableApiPlatformTrait;
use Atournayre\Bundle\MakerBundle\Traits\Config\PropertiesAllowedTypesTrait;
use Atournayre\Bundle\MakerBundle\Traits\Config\PropertiesAccessorsTrait;
use function Symfony\Component\String\u;

class TraitForEntityMakerConfiguration extends MakerConfiguration
{
    use PropertiesAccessorsTrait;
    use PropertiesAllowedTypesTrait;
    use EnableApiPlatformTrait;

    private bool $isUsedByEntity = false;

    public static function fromFqcn(string $rootDir, string $rootNamespace, string $fqcn,): static
    {
        $fqcn = u($fqcn)->ensureEnd('Trait')->toString();

        return self::fromFqcn($rootDir, $rootNamespace, $fqcn);
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
