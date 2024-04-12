<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\VO\Builder\InterfaceBuilder;
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
            ->addArgument('namespace', InputArgument::REQUIRED, 'The namespace of the interface <fg=yellow>(e.g. App\Contracts\DummyInterface)</>');
    }

    protected function configurations(string $namespace): array
    {
        return [
            new MakerConfig(
                namespace: $namespace,
                builder: InterfaceBuilder::class,
                classnameSuffix: 'Interface',
            ),
        ];
    }
}
