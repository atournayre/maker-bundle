<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
use Atournayre\Bundle\MakerBundle\Contracts\PhpFileBuilderInterface;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;

abstract class AbstractBuilder implements PhpFileBuilderInterface
{
    protected PhpFileDefinition $phpFileDefinition;

    abstract public function supports(string $makerConfigurationClassName): bool;

    public function create(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition = PhpFileDefinition::create(
            $makerConfiguration->namespace(),
            $makerConfiguration->classname()
        );
        $this->setStrictTypes($makerConfiguration);
        $this->setComments($makerConfiguration);
        $this->addUses($makerConfiguration);
        $this->addAttributes($makerConfiguration);
        $this->setExtends($makerConfiguration);
        $this->addImplements($makerConfiguration);
        $this->addConstants($makerConfiguration);
        $this->addTraits($makerConfiguration);
        $this->addProperties($makerConfiguration);
        $this->addMethods($makerConfiguration);
    }

    public function createInstance(MakerConfigurationInterface $makerConfiguration): PhpFileDefinition
    {
        return PhpFileDefinition::create(
            $makerConfiguration->namespace(),
            $makerConfiguration->classname()
        )->setComments([
            'This file has been auto-generated',
        ]);
    }

    public function setStrictTypes(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setStrictTypes(true);
    }

    public function setComments(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setComments([
            'This file has been auto-generated',
        ]);
    }

    public function addUses(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setUses([]);
    }

    public function addAttributes(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setAttributes([]);
    }

    public function setInterface(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setInterface(false);
    }

    public function setTrait(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setTrait(false);
    }

    public function setReadonly(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setReadonly(true);
    }

    public function setFinal(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setFinal(true);
    }

    public function setExtends(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setExtends(null);
    }

    public function addImplements(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setImplements([]);
    }

    public function addConstants(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setConstants([]);
    }

    public function addTraits(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setTraits([]);
    }

    public function addProperties(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setProperties([]);
    }

    public function addMethods(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setMethods([]);
    }

    /**
     * @throws \Exception
     */
    public function setNamespace(MakerConfigurationInterface $makerConfiguration): void
    {
        throw new \Exception('Use constructor to set namespace');
    }

    /**
     * @throws \Exception
     */
    public function setClassName(MakerConfigurationInterface $makerConfiguration): void
    {
        throw new \Exception('Use constructor to set class name');
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
