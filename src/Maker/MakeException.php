<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\VO\Builder\ExceptionBuilder;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeException extends AbstractMaker
{
    private string $exceptionType;
    private ?string $exceptionNamedConstructor = null;

    public static function getCommandName(): string
    {
        return 'make:new:exception';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new exception')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The namespace of the interface <fg=yellow>(e.g. App\Exception\Dummy)</>');
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Exception';
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

        $questionExceptionType = new ChoiceQuestion('Choose the exception type', [
            \Exception::class,
            \RuntimeException::class,
            \InvalidArgumentException::class,
            \LogicException::class,
        ]);

        $exceptionType = $io->askQuestion($questionExceptionType);

        $this->exceptionType = $exceptionType;

        $questionNamedConstructor = new Question('Add a named constructor. Leave empty to skip.');
        $namedConstructor = (string)$io->askQuestion($questionNamedConstructor);

        if ('' === $namedConstructor) {
            return;
        }

        $this->exceptionNamedConstructor = $namedConstructor;
    }

    protected function configurations(string $namespace): array
    {
        return [
            (new MakerConfig(
                namespace: $namespace,
                builder: ExceptionBuilder::class,
                classnameSuffix: '',
            ))
                ->withExtraProperty('exceptionType', $this->exceptionType)
                ->withExtraProperty('exceptionNamedConstructor', $this->exceptionNamedConstructor),
        ];
    }
}
