<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\InterfaceType;

class UserInterfaceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string $namespace = '',
        string $name = ''
    ): FileDefinitionBuilder
    {
        $namespace = 'Contracts\\Security';
        $name = 'User';

        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Interface', $config);

        $interface = $fileDefinition->file->addInterface($fileDefinition->fullName());

        self::getRoles($interface);
        self::getPassword($interface);
        self::getSalt($interface);
        self::getUsername($interface);
        self::eraseCredentials($interface);

        return $fileDefinition;
    }

    private static function getRoles(InterfaceType $interface): void
    {
        $interface->addMethod('getRoles')
            ->setPublic()
            ->addBody('Returns the roles granted to the user.')
            ->addBody('    public function getRoles()')
            ->addBody('    {')
            ->addBody('        return [\'ROLE_USER\'];')
            ->addBody('    }')
            ->addBody('Alternatively, the roles might be stored in a ``roles`` property,')
            ->addBody('and populated in any number of different ways when the user object')->addBody('is created.')
            ->addBody('@return array The user roles')
        ;
    }

    private static function getPassword(InterfaceType $interface): void
    {
        $interface->addMethod('getPassword')
            ->setPublic()
            ->addBody('Returns the password used to authenticate the user.')
            ->addBody('This should be the encoded password. On authentication, a plain-text')
            ->addBody('password will be salted, encoded, and then compared to this value.')
            ->addBody('@return string|null The encoded password if any')
            ;
    }

    private static function getSalt(InterfaceType $interface): void
    {
        $interface->addMethod('getSalt')
            ->setPublic()
            ->addBody('Returns the salt that was originally used to encode the password.')
            ->addBody('This can return null if the password was not encoded using a salt.')
            ->addBody('@return string|null The salt')
            ;
    }

    private static function getUsername(InterfaceType $interface): void
    {
        $interface->addMethod('getUsername')
            ->setPublic()
            ->addBody('Returns the username used to authenticate the user.')
            ->addBody('@return string The username')
        ;
    }

    private static function eraseCredentials(InterfaceType $interface): void
    {
        $interface->addMethod('eraseCredentials')
            ->setPublic()
            ->addBody('Removes sensitive data from the user.')
            ->addBody('This is important if, at any given point, sensitive information like')
            ->addBody('the plain-text password is stored on this object.')
        ;
    }
}
