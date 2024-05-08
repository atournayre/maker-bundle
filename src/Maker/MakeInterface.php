<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Config\InterfaceMakerConfiguration;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeInterface extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:new:interface';
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Interface';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new interface')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The class name of the interface <fg=yellow>(e.g. DummyInterface)</>');
    }

    /**
     * @param string $namespace
     * @return MakerConfigurationCollection
     * @throws \Throwable
     */
    protected function configurations(string $namespace): MakerConfigurationCollection
    {
        return MakerConfigurationCollection::createAsList([
            InterfaceMakerConfiguration::fromNamespace(
                rootDir: $this->rootDir,
                rootNamespace: $this->rootNamespace,
                namespace: $this->configNamespaces->contracts,
                className: $namespace,
            )
        ]);
    }
}
