<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Contracts;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use Nette\PhpGenerator\Method;

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

        $fileDefinition
            ->file
            ->addInterface($fileDefinition->fullName())
            ->addMember(self::getRoles())
            ->addMember(self::getPassword())
            ->addMember(self::getSalt())
            ->addMember(self::getUsername())
            ->addMember(self::eraseCredentials())
        ;

        return $fileDefinition;
    }

    private static function getRoles(): Method
    {
        $method = new Method('getRoles');
        $method
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
        return $method;
    }

    private static function getPassword(): Method
    {
        $method = new Method('getPassword');
        $method
            ->setPublic()
            ->addBody('Returns the password used to authenticate the user.')
            ->addBody('This should be the encoded password. On authentication, a plain-text')
            ->addBody('password will be salted, encoded, and then compared to this value.')
            ->addBody('@return string|null The encoded password if any')
        ;
        return $method;
    }

    private static function getSalt(): Method
    {
        $method = new Method('getSalt');
        $method
            ->setPublic()
            ->addBody('Returns the salt that was originally used to encode the password.')
            ->addBody('This can return null if the password was not encoded using a salt.')
            ->addBody('@return string|null The salt')
        ;
        return $method;
    }

    private static function getUsername(): Method
    {
        $method = new Method('getUsername');
        $method
            ->setPublic()
            ->addBody('Returns the username used to authenticate the user.')
            ->addBody('@return string The username')
        ;
        return $method;
    }

    private static function eraseCredentials(): Method
    {
        $method = new Method('eraseCredentials');
        $method
            ->setPublic()
            ->addBody('Removes sensitive data from the user.')
            ->addBody('This is important if, at any given point, sensitive information like')
            ->addBody('the plain-text password is stored on this object.')
        ;
        return $method;
    }
}
