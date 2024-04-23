<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use App\Command\AbstractCommand;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\Method;
use Webmozart\Assert\Assert;

class CommandBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $config = $fileDefinition->configuration();

        Assert::true($config->hasExtraProperty('title'), 'The configuration should have a title property');
        Assert::true($config->hasExtraProperty('description'), 'The configuration should have a description property');
        Assert::true($config->hasExtraProperty('commandName'), 'The configuration should have a commandName property');

        $attributes = [
            new Attribute(\Symfony\Component\Console\Attribute\AsCommand::class, [
                'name' => $config->getExtraProperty('commandName'),
                'description' => $config->getExtraProperty('description'),
            ]),
        ];

        return (new self($fileDefinition))
            ->createFile()
            ->withUse(\Symfony\Component\Console\Attribute\AsCommand::class)
            ->withUse(\Symfony\Component\Console\Input\InputInterface::class)
            ->withUse(\Symfony\Component\Console\Style\SymfonyStyle::class)
            ->setAttributes($attributes)
            ->extends(AbstractCommand::class)
            ->addMember(self::titleMethod($fileDefinition))
            ->addMember(self::doExecuteMethod())
            ->addMember(self::preConditionsChecks())
            ->addMember(self::failFast())
            ->addMember(self::postConditionsChecks())
        ;
    }

    private static function titleMethod(FileDefinition $fileDefinition): Method
    {
        $title = $fileDefinition->configuration()->getExtraProperty('title');

        return (new Method('title'))
            ->setPublic()
            ->setReturnType('string')
            ->setBody('return \''.$title.'\';')
        ;
    }

    private static function doExecuteMethod(): Method
    {
        $method = new Method('doExecute');
        $method->setPublic()->setReturnType('void');
        $method->addParameter('input')->setType(\Symfony\Component\Console\Input\InputInterface::class);
        $method->addParameter('io')->setType(\Symfony\Component\Console\Style\SymfonyStyle::class);
        $method->setBody('// Add your logic here');
        return $method;
    }

    private static function preConditionsChecks(): Method
    {
        $method = new Method('preConditionsChecks');
        $method->setProtected()->setReturnType('void');
        $method->addParameter('input')->setType(\Symfony\Component\Console\Input\InputInterface::class);
        $method->addParameter('io')->setType(\Symfony\Component\Console\Style\SymfonyStyle::class);
        $method->setBody('// Implement method or remove it if not needed');
        return $method;
    }

    private static function failFast(): Method
    {
        $method = new Method('failFast');
        $method->setProtected()->setReturnType('void');
        $method->addParameter('input')->setType(\Symfony\Component\Console\Input\InputInterface::class);
        $method->addParameter('io')->setType(\Symfony\Component\Console\Style\SymfonyStyle::class);
        $method->setBody('// Implement method or remove it if not needed');
        return $method;
    }

    private static function postConditionsChecks(): Method
    {
        $method = new Method('postConditionsChecks');
        $method->setProtected()->setReturnType('void');
        $method->addParameter('input')->setType(\Symfony\Component\Console\Input\InputInterface::class);
        $method->addParameter('io')->setType(\Symfony\Component\Console\Style\SymfonyStyle::class);
        $method->setBody('// Implement method or remove it if not needed');
        return $method;
    }
}
