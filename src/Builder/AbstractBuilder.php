<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
use Atournayre\Bundle\MakerBundle\Contracts\PhpFileBuilderInterface;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;

abstract class AbstractBuilder implements PhpFileBuilderInterface
{
    abstract public function supports(string $makerConfigurationClassName): bool;

    /**
     * @param MakerConfigurationInterface $makerConfiguration
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        return PhpFileDefinition::create(
            $makerConfiguration->namespace(),
            $makerConfiguration->classname()
        )->setComments([
            'This file has been auto-generated',
        ]);
    }
}
