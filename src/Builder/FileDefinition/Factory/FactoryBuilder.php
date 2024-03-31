<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Factory;

use App\VO\Null\NullUser;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Method;

class FactoryBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = 'Factory',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Factory', $config);

        $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setReadOnly()
        ;

        return $fileDefinition;
    }

    public static function buildContext(
        MakerConfig $config,
        string $namespace = 'Factory',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $methodConstruct = new Method('__construct');
        $methodConstruct
            ->setPublic()
            ->addPromotedParameter('security')
            ->setType(\App\Contracts\Security\SecurityInterface::class);
        $methodConstruct
            ->addPromotedParameter('clock')
            ->setType(\Psr\Clock\ClockInterface::class);

        $methodCreate = new Method('create');
        $methodCreate
            ->setPublic()
            ->addBody('return Context::create(')
            ->addBody('    $user ?? $this->security->getUser() ?? NullUser::create(),')
            ->addBody('    $dateTime ?? $this->clock->now()')
            ->addBody(');')
            ->setReturnType(\App\VO\Context::class)
            ->addComment('@throws \Exception');
        $methodCreate
            ->addParameter('user')
            ->setType(\App\Contracts\Security\UserInterface::class)
            ->setDefaultValue(null);
        $methodCreate
            ->addParameter('dateTime')
            ->setType(\DateTimeInterface::class)
            ->setDefaultValue(null);

        $name = 'Context';

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Factory', $config);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setReadOnly()
            ->addMember($methodConstruct)
            ->addMember($methodCreate)
        ;

        $class->getNamespace()
            ->addUse(\App\Contracts\Security\SecurityInterface::class)
            ->addUse(\Psr\Clock\ClockInterface::class)
            ->addUse(\App\VO\Context::class)
            ->addUse(\App\Contracts\Security\UserInterface::class)
            ->addUse(\App\VO\Null\NullUser::class)
        ;

        return $fileDefinition;
    }
}
