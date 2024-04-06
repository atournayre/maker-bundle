<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Webmozart\Assert\Assert;

abstract class AbstractServiceGenerator extends AbstractGenerator
{
    abstract protected function serviceDefinition(MakerConfig $config, string $namespace, string $name): FileDefinitionBuilder;

    /**
     * @throws \Exception
     */
    public function generate(string $namespace, string $name, MakerConfig $config): void
    {
        $config = $this->addRootToConfig($config);

        $this->checkIfCommandAndQueryServicesExists($config);

        $serviceCommand = $this->serviceDefinition($config, $namespace, $name);

        $filesDefinitions = [];
        $filesDefinitions[] = $serviceCommand;
        $filesDefinitions[] = $this->addAttributeToVO($config, $serviceCommand);

        $this->addFileDefinition($filesDefinitions);
        $this->generateFiles();
    }

    private function checkIfCommandAndQueryServicesExists(MakerConfig $config): void
    {
        $classNameCommandService = $config->rootNamespace().'\\Service\\CommandService';
        Assert::classExists($classNameCommandService, 'CommandService class does not exist');

        $classNameQueryService = $config->rootNamespace().'\\Service\\QueryService';
        Assert::classExists($classNameQueryService, 'QueryService class does not exist');
    }

    abstract protected function attribute(MakerConfig $config): string;

    /**
     * @throws \Exception
     */
    private function addAttributeToVO(MakerConfig $config, FileDefinitionBuilder $fileDefinitionBuilder): FileDefinitionBuilder
    {
        $vo = $config->rootNamespace() . '\\' . $config->extraProperties()['vo'];

        $voBuilder = FileDefinitionBuilder::buildFrom($config, $vo);

        $class = $voBuilder->getClass();

        $attribute = $this->attribute($config);

        $namespace = $class->getNamespace();
        $namespace->addUse($attribute);
        $namespace->addUse($fileDefinitionBuilder->fullName());

        $this->checkIfAttributeAlreadyExists($vo, $class, $attribute);

        $serviceFqdn = $fileDefinitionBuilder->className() . '::class';
        $class->addAttribute($attribute, [
            'serviceName' => new Literal($serviceFqdn),
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
