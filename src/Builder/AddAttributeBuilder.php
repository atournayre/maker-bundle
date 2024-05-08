<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\AddAttributeMakerConfiguration;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;

class AddAttributeBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === AddAttributeMakerConfiguration::class;
    }

    /**
     * @param AddAttributeMakerConfiguration $makerConfiguration
     * @return PhpFileDefinition
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        return parent::createPhpFileDefinition($makerConfiguration)
            ->setUses([$makerConfiguration->serviceNamespace()])
            ->setAttributes($makerConfiguration->attributes())
        ;
    }
}
