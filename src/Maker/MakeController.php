<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\Builder\ControllerBuilder;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

#[AutoconfigureTag('maker.command')]
class MakeController extends AbstractMaker
{
    private ?string $controllerRelatedEntity = null;
    private ?string $controllerRelatedFormType = null;
    private ?string $controllerRelatedVO = null;

    public static function getCommandName(): string
    {
        return 'make:new:controller';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new Controller')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The namespace of the Controller <fg=yellow>(e.g. App\\\\Controller\\\\DummyController)</>');
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Controller';
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if (empty($this->entities())) {
            $io->error('No entity found in the Entity directory');
            return;
        }

        if (empty($this->formTypes())) {
            $io->error('No form types found in the FormType directory');
            return;
        }

        if (empty($this->vos())) {
            $io->error('No VO found in the VO directory');
            return;
        }

        parent::interact($input, $io, $command);

        $questionControllerRelatedEntity = new ChoiceQuestion('Choose the entity related to this Controller', $this->entities());
        $this->controllerRelatedEntity = $io->askQuestion($questionControllerRelatedEntity);

        $questionControllerRelatedFormType = new ChoiceQuestion('Choose the form type related to this Controller', $this->formTypes());
        $this->controllerRelatedFormType = $io->askQuestion($questionControllerRelatedFormType);

        $questionControllerRelatedVO = new ChoiceQuestion('Choose the VO related to this Controller', $this->vos());
        $this->controllerRelatedVO = $io->askQuestion($questionControllerRelatedVO);
    }

    private function entities(): array
    {
        return $this->findFilesInDirectory(Str::sprintf('%s/Entity', $this->rootDir));
    }

    private function formTypes(): array
    {
        return $this->findFilesInDirectory(Str::sprintf('%s/Form', $this->rootDir));
    }

    private function vos(): array
    {
        return $this->findFilesInDirectory(Str::sprintf('%s/VO/Entity', $this->rootDir));
    }

    private function findFilesInDirectory(string|array $directory): array
    {
        $directories = array_filter(
            is_array($directory) ? $directory : [$directory],
            fn($directory) => (new Filesystem())->exists($directory)
        );

        if ([] === $directories) {
            return [];
        }

        $finder = (new Finder())
            ->files()
            ->in($directories)
            ->name('*.php')
            ->sortByName();

        $files = [];
        foreach ($finder as $file) {
            $files[] = Str::namespaceFromPath($file->getPathname(), $this->rootDir);
        }
        return $files;
    }

    protected function configurations(string $namespace): array
    {
        $configurations[] = (new MakerConfig(
            namespace: $namespace,
            builder: ControllerBuilder::class,
        ))
            ->withTemplatePathKeepingNamespace('Controller/WithFormController.php')
            ->withExtraProperty('entity', Str::prefixByRootNamespace($this->controllerRelatedEntity, $this->rootNamespace))
            ->withExtraProperty('formType', Str::prefixByRootNamespace($this->controllerRelatedFormType, $this->rootNamespace))
            ->withExtraProperty('vo', Str::prefixByRootNamespace($this->controllerRelatedVO, $this->rootNamespace));

        return $configurations ?? [];
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $deps = [
            \Symfony\Component\Form\Extension\Core\Type\FormType::class => 'symfony/form',
            \Symfony\Component\Form\FormInterface::class => 'symfony/form',
        ];

        foreach ($deps as $class => $package) {
            $dependencies->addClassDependency($class, $package);
        }
    }
}
