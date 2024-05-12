<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use ArchTech\Enums\Comparable;
use ArchTech\Enums\From;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Metadata;
use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;
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
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        return parent::createPhpFileDefinition($makerConfiguration)
            ->setEnum()
            ->setEnumCases($makerConfiguration->enumCases())
            ->setEnumType($makerConfiguration->enumType())
            ->setTraits([
                Comparable::class,
                From::class,
                InvokableCases::class,
                Metadata::class,
                Names::class,
                Options::class,
                Values::class,
            ])
        ;
    }
}
