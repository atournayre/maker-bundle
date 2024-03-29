<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service\CommandAndQueryServicesBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\VOBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Generator\AbstractGenerator;
use Nette\PhpGenerator\ClassType;

abstract class AbstractServiceGenerator extends AbstractGenerator
{
    abstract protected function serviceDefinition(MakerConfig $config, string $namespace, string $name): FileDefinitionBuilder;

    /**
     * @throws \Exception
     */
    public function generate(string $namespace, string $name, MakerConfig $config): void
    {
        $config = $this->addRootToConfig($config);

        $this->createCommandAndQueryServiceIfNotExists($config);

        $serviceCommand = $this->serviceDefinition($config, $namespace, $name);

        $filesDefinitions = [];
        $filesDefinitions[] = $serviceCommand;
        $filesDefinitions[] = $this->addAttributeToVO($config, $serviceCommand);

        $this->addFileDefinition($filesDefinitions);
        $this->generateFiles();
    }

    private function createCommandAndQueryServiceIfNotExists(MakerConfig $config): void
    {
        $classNameCommandService = $config->rootNamespace().'\\Service\\CommandService';
        $classNameQueryService = $config->rootNamespace().'\\Service\\QueryService';

        if (class_exists($classNameCommandService) && class_exists($classNameQueryService)) {
            return;
        }

        $this->addFileDefinition(CommandAndQueryServicesBuilder::filesDefinitions($config));
        $this->generateFiles();
        $this->clearFilesDefinitions();
    }

    abstract protected function attribute(MakerConfig $config): string;

    /**
     * @throws \Exception
     */
    private function addAttributeToVO(MakerConfig $config, FileDefinitionBuilder $fileDefinitionBuilder): FileDefinitionBuilder
    {
        $vo = $config->rootNamespace() . '\\' . $config->extraProperties()['vo'];

        $voBuilder = VOBuilder::from($config, $vo);

        $class = $voBuilder->getClass();

        $attribute = $this->attribute($config);

        $namespace = $class->getNamespace();
        $namespace->addUse($attribute);

        $this->checkIfAttributeAlreadyExists($vo, $class, $attribute);

        $class->addAttribute($attribute, [
            'serviceName' => '\\'.$fileDefinitionBuilder->fullName().'::class',
        ]);

        return $voBuilder;
    }

    /**
     * @throws \Exception
     */
    private function checkIfAttributeAlreadyExists(string $vo, ClassType $class, string $attribute): void
    {
        $attributes = array_unique(array_map(fn($attribute) => $attribute->getName(), $class->getAttributes()));

        if (in_array($attribute, $attributes)) {
            throw new \Exception(sprintf('Attribute %s already exists in %s', $attribute, $vo));
        }
    }
}
