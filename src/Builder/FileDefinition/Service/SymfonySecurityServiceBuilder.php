<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service;

use App\Contracts\Security\UserInterface;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Method;

class SymfonySecurityServiceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'Service\\Security',
        string      $name = 'SymfonySecurityService'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Service', $config);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setReadOnly()
            ->addImplement(\App\Contracts\Security\SecurityInterface::class)
            ->addMember(self::addConstruct())
            ->addMember(self::getUser())
        ;

        $class->getNamespace()
            ->addUse(\Symfony\Bundle\SecurityBundle\Security::class)
            ->addUse(\App\Contracts\Security\SecurityInterface::class)
            ->addUse(\App\Contracts\Security\UserInterface::class)
            ->addUse(\App\VO\Null\NullUser::class)
        ;

        return $fileDefinition;
    }

    private static function addConstruct(): Method
    {
        $method = new Method('__construct');
        $method->setPublic();

        $method
            ->addPromotedParameter('security')
            ->setPrivate()
            ->setType(\Symfony\Bundle\SecurityBundle\Security::class);
        return $method;
    }

    private static function getUser(): Method
    {
        $method = new Method('getUser');
        $method
            ->setPublic()
            ->setReturnType(\App\Contracts\Security\UserInterface::class)
            ->addBody('/** @var UserInterface $user */')
            ->addBody('$user = $this->security->getUser();')
            ->addBody('return $user ?? NullUser::create();');
        return $method;
    }
}
