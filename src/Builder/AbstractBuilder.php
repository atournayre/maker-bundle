<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
use Atournayre\Bundle\MakerBundle\Contracts\PhpFileBuilderInterface;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;

abstract class AbstractBuilder implements PhpFileBuilderInterface
{
    abstract public function supports(string $makerConfigurationClassName): bool;

    /**
     * @param MakerConfigurationInterface $makerConfiguration
     * @return PhpFileDefinition
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

    /**
     * @param MakerConfigurationInterface $makerConfiguration
     * @return array<string, string>
     */
    protected function correspondingTypes(MakerConfigurationInterface $makerConfiguration): array
    {
        $rootDir = $makerConfiguration->rootDir();
        $allowedTypes = $makerConfiguration->propertiesAllowedTypes();

        $allowedTypesMapping = [];
        foreach ($allowedTypes as $allowedType) {
            if (!str_contains($allowedType, '/')) {
                $allowedTypesMapping[$allowedType] = $allowedType;
                continue;
            }
            $namespaceFromPath = Str::namespaceFromPath($allowedType, $rootDir);
            $rootNamespace = $makerConfiguration->rootNamespace();
            $namespace = Str::prefixByRootNamespace($namespaceFromPath, $rootNamespace);
            $allowedTypesMapping[$allowedType] = $namespace;
        }
        return $allowedTypesMapping;
    }
}
