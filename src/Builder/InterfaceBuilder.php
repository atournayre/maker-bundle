<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\InterfaceMakerConfiguration;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;

final class InterfaceBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === InterfaceMakerConfiguration::class;
    }

    /**
     * @param InterfaceMakerConfiguration $makerConfiguration
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        return parent::createPhpFileDefinition($makerConfiguration)
            ->setInterface()
        ;
    }
}
