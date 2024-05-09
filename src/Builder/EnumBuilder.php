<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\EnumMakerConfiguration;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;

final class EnumBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === EnumMakerConfiguration::class;
    }

    /**
     * @param EnumMakerConfiguration $makerConfiguration
     * @return PhpFileDefinition
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        return parent::createPhpFileDefinition($makerConfiguration)
            ->setEnum()
            ->setEnumCases($makerConfiguration->enumCases())
            ->setEnumType($makerConfiguration->enumType())
            ->setTraits([
                \ArchTech\Enums\Comparable::class,
                \ArchTech\Enums\From::class,
                \ArchTech\Enums\InvokableCases::class,
                \ArchTech\Enums\Metadata::class,
                \ArchTech\Enums\Names::class,
                \ArchTech\Enums\Options::class,
                \ArchTech\Enums\Values::class,
            ])
        ;
    }
}
