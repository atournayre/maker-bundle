<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Factory;

use App\VO\Null\NullUser;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;

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

        return $fileDefinition;
    }

    public static function buildContext(
        MakerConfig $config,
        string $namespace = 'Factory',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $name = 'Context';

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Factory', $config);
        $fileDefinition->file->addClass($fileDefinition->fullName());

        $class = $fileDefinition->getClass();
        $class->setFinal()->setReadOnly();

        $namespace = $class->getNamespace();
        $namespace->addUse(\App\Contracts\Security\SecurityInterface::class);
        $namespace->addUse(\Psr\Clock\ClockInterface::class);
        $namespace->addUse(\App\VO\Context::class);
        $namespace->addUse(\App\Contracts\Security\UserInterface::class);


        $class->addMethod('__construct')
            ->setPublic()
            ->addPromotedParameter('security')
            ->setType(\App\Contracts\Security\SecurityInterface::class);

        $class->getMethod('__construct')
            ->addPromotedParameter('clock')
            ->setType(\Psr\Clock\ClockInterface::class);

        $class->addMethod('create')
            ->setPublic()
            ->addBody('return Context::create(')
            ->addBody('    $user ?? $this->security->getUser() ?? NullUser::create(),')
            ->addBody('    $dateTime ?? $this->clock->now()')
            ->addBody(');')
            ->setReturnType(\App\VO\Context::class)
            ->addComment('@throws \Exception');

        $class->getMethod('create')
            ->addParameter('user')
            ->setType(\App\Contracts\Security\UserInterface::class)
            ->setDefaultValue(null);

        $class->getMethod('create')
            ->addParameter('dateTime')
            ->setType(\DateTimeInterface::class)
            ->setDefaultValue(null);

        return $fileDefinition;

    }
}
