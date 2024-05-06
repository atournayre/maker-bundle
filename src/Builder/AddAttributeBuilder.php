<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\AddAttributeMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;

class AddAttributeBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === AddAttributeMakerConfiguration::class;
    }

    public function createInstance(MakerConfigurationInterface|AddAttributeMakerConfiguration $makerConfiguration): PhpFileDefinition
    {
        return parent::createInstance($makerConfiguration)
            ->setUses([$makerConfiguration->serviceNamespace()])
            ->setAttributes($makerConfiguration->attributes())
        ;
    }
}
