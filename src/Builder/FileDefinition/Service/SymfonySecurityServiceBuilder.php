<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service;

use App\Contracts\Security\UserInterface;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\ClassType;

class SymfonySecurityServiceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'Service\\Security',
        string      $name = 'SymfonySecurityService'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Service', $config);

        $class = $fileDefinition->file->addClass($fileDefinition->fullName());
        $class->setFinal()->setReadOnly();
        $class->addImplement(\App\Contracts\Security\SecurityInterface::class);;

        $class->getNamespace()
            ->addUse(\Symfony\Bundle\SecurityBundle\Security::class)
            ->addUse(\App\Contracts\Security\SecurityInterface::class)
            ->addUse(\App\Contracts\Security\UserInterface::class)
            ->addUse(\App\VO\Null\NullUser::class)
        ;

        self::addConstruct($class);
        self::getUser($class);

        return $fileDefinition;
    }

    private static function addConstruct(ClassType $class): void
    {
        $class->addMethod('__construct')
            ->setPublic();

        $class->getMethod('__construct')
            ->addPromotedParameter('security')
            ->setPrivate()
            ->setType(\Symfony\Bundle\SecurityBundle\Security::class);
    }

    private static function getUser(ClassType $class): void
    {
        $namespace = $class->getNamespace();
        $namespace->addUse(\App\Contracts\Security\UserInterface::class);

        $class->addMethod('getUser')
            ->setReturnType(\App\Contracts\Security\UserInterface::class);

        $class->getMethod('getUser')
            ->addBody('/** @var UserInterface $user */')
            ->addBody('$user = $this->security->getUser();')
            ->addBody('return $user ?? NullUser::create();');
    }
}
