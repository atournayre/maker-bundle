<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Factory;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\ClassType;

class FactoryBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'Factory',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Factory', $config);
        $fileDefinition->file->addClass($fileDefinition->fullName());

        $class = $fileDefinition->getClass();
        $class->setFinal()->setReadOnly();

        $namespace = $class->getNamespace();
        $namespace->addUse(\App\Contracts\Security\SecurityInterface::class);
        $namespace->addUse(\Psr\Clock\ClockInterface::class);
        $namespace->addUse(\App\VO\Context::class);

        self::construct($class);
        self::create($class);

        return $fileDefinition;
    }

    private static function construct(ClassType $class): void
    {
        $class->addMethod('__construct')
            ->setPublic()
            ->addPromotedParameter('security')
            ->setType(\App\Contracts\Security\SecurityInterface::class);

        $class->getMethod('__construct')
            ->addPromotedParameter('clock')
            ->setType(\Psr\Clock\ClockInterface::class);
    }

    private static function create(ClassType $class): void
    {
        $class->addMethod('create')
            ->setPublic()
            ->addBody('return Context::create($this->security->getUser(), $this->clock->now());')
            ->setReturnType(\App\VO\Context::class)
            ->addComment('@throws \Exception')
        ;
    }
}
