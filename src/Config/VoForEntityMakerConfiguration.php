<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Atournayre\Bundle\MakerBundle\Traits\Config\PropertiesAllowedTypesTrait;
use Atournayre\Bundle\MakerBundle\Traits\Config\PropertiesAccessorsTrait;

class VoForEntityMakerConfiguration extends MakerConfiguration
{
    use PropertiesAccessorsTrait;
    use PropertiesAllowedTypesTrait;

    private string $relatedEntity = '';

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
