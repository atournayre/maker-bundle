<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use App\Command\AbstractCommand;
use Atournayre\Bundle\MakerBundle\Config\CommandMakerConfiguration;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\Method;

final class CommandBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === CommandMakerConfiguration::class;
    }

    /**
     * @param CommandMakerConfiguration $makerConfiguration
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        return parent::createPhpFileDefinition($makerConfiguration)
            ->setUses([
                \Symfony\Component\Console\Attribute\AsCommand::class,
                \Symfony\Component\Console\Input\InputInterface::class,
                \Symfony\Component\Console\Style\SymfonyStyle::class,
            ])
            ->setAttributes($this->attributes($makerConfiguration))
            ->setExtends(AbstractCommand::class)
            ->setMethods([
                $this->titleMethod($makerConfiguration),
                $this->doExecuteMethod(),
                $this->preConditionsChecks(),
                $this->failFast(),
                $this->postConditionsChecks(),
            ])
        ;
    }

    /**
     * @return array<Attribute>
     */
    private function attributes(CommandMakerConfiguration $makerConfiguration): array
    {
        return [
            new Attribute(\Symfony\Component\Console\Attribute\AsCommand::class, [
                'name' => $makerConfiguration->commandName(),
                'description' => $makerConfiguration->description(),
            ]),
        ];
    }

    private function titleMethod(CommandMakerConfiguration $makerConfiguration): Method
    {
        $title = $makerConfiguration->title();

        return (new Method('title'))
            ->setPublic()
            ->setReturnType('string')
            ->setBody('return \''.$title.'\';')
            ;
    }

    private function doExecuteMethod(): Method
    {
        $method = new Method('doExecute');
        $method->setPublic()->setReturnType('void');
        $method->addParameter('input')->setType(\Symfony\Component\Console\Input\InputInterface::class);
        $method->addParameter('io')->setType(\Symfony\Component\Console\Style\SymfonyStyle::class);
        $method->setBody('// Add your logic here');
        return $method;
    }

    private function preConditionsChecks(): Method
    {
        $method = new Method('preConditionsChecks');
        $method->setProtected()->setReturnType('void');
        $method->addParameter('input')->setType(\Symfony\Component\Console\Input\InputInterface::class);
        $method->addParameter('io')->setType(\Symfony\Component\Console\Style\SymfonyStyle::class);
        $method->setBody('// Implement method or remove it if not needed');
        return $method;
    }

    private function failFast(): Method
    {
        $method = new Method('failFast');
        $method->setProtected()->setReturnType('void');
        $method->addParameter('input')->setType(\Symfony\Component\Console\Input\InputInterface::class);
        $method->addParameter('io')->setType(\Symfony\Component\Console\Style\SymfonyStyle::class);
        $method->setBody('// Implement method or remove it if not needed');
        return $method;
    }

    private function postConditionsChecks(): Method
    {
        $method = new Method('postConditionsChecks');
        $method->setProtected()->setReturnType('void');
        $method->addParameter('input')->setType(\Symfony\Component\Console\Input\InputInterface::class);
        $method->addParameter('io')->setType(\Symfony\Component\Console\Style\SymfonyStyle::class);
        $method->setBody('// Implement method or remove it if not needed');
        return $method;
    }
}
