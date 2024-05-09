<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Config\ExceptionMakerConfiguration;
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
            ->addArgument('namespace', InputArgument::REQUIRED, 'The class name of the exception <fg=yellow>(e.g. Dummy)</>');
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

    /**
     * @throws \Throwable
     */
    protected function configurations(string $namespace): MakerConfigurationCollection
    {
        return MakerConfigurationCollection::createAsList([
            ExceptionMakerConfiguration::fromNamespace(
                rootDir: $this->rootDir,
                rootNamespace: $this->rootNamespace,
                namespace: $this->configNamespaces->exception,
                className: $namespace,
            )
                ->withType($this->exceptionType)
                ->withNamedConstructor($this->exceptionNamedConstructor),
        ]);
    }
}
