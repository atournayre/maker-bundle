<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Generator\ExceptionGenerator;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeException extends AbstractMaker
{
    private string $exceptionName;
    private string $exceptionType;
    private string $exceptionNamedConstructor = '';

    public function __construct(
        private readonly ExceptionGenerator $exceptionGenerator,
    )
    {
    }

    public static function getCommandName(): string
    {
        return 'make:new:exception';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new exception');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // no-op
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $io->title('Creating new Exception');
        $path = 'Exception';

        $config = MakerConfig::default()
            ->withExtraProperty('exceptionType', $this->exceptionType)
            ->withExtraProperty('exceptionNamedConstructor', $this->exceptionNamedConstructor);

        $this->exceptionGenerator->generate($path, $this->exceptionName, $config);

        $this->writeSuccessMessage($io);

        foreach ($this->exceptionGenerator->getGeneratedFiles() as $file) {
            $io->text(sprintf('Created: %s', $file));
        }
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new exception';
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

        $questionName = new Question('Choose the exception name');
        $exceptionName = $io->askQuestion($questionName);

        $this->exceptionName = $exceptionName;

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
}
