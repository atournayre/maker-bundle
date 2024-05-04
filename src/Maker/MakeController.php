<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Helper\MakeHelper;
use Atournayre\Bundle\MakerBundle\VO\Builder\ControllerBuilder;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeController extends AbstractMaker
{
    private ?string $controllerRelatedEntity = null;
    private ?string $controllerRelatedFormType = null;
    private ?string $controllerRelatedVO = null;
    private bool $useAForm = false;

    public static function getCommandName(): string
    {
        return 'make:new:controller';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new Controller')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The class name of the Controller <fg=yellow>(e.g. DummyController)</>');
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Controller';
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

        $questionWithForm = new Question('Create a controller with a form? (yes/no)', 'yes');
        $this->useAForm = $io->askQuestion($questionWithForm) === 'yes';

        if (empty($this->entities())) {
            $io->error('No entity found in the Entity directory');
            return;
        }

        $questionControllerRelatedEntity = new ChoiceQuestion('Choose the entity related to this Controller', $this->entities());
        $this->controllerRelatedEntity = $io->askQuestion($questionControllerRelatedEntity);

        if ($this->useAForm) {
            $this->interactWithForm($input, $io, $command);
            return;
        }

        $this->interactSimple($input, $io, $command);
    }

    public function interactWithForm(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if (empty($this->formTypes())) {
            $io->error('No form types found in the FormType directory');
            return;
        }

        if (empty($this->vos())) {
            $io->error('No VO found in the VO directory');
            return;
        }

        $questionControllerRelatedFormType = new ChoiceQuestion('Choose the form type related to this Controller', $this->formTypes());
        $this->controllerRelatedFormType = $io->askQuestion($questionControllerRelatedFormType);

        $questionControllerRelatedVO = new ChoiceQuestion('Choose the VO related to this Controller', $this->vos());
        $this->controllerRelatedVO = $io->askQuestion($questionControllerRelatedVO);
    }

    public function interactSimple(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        throw new \RuntimeException('Creating controller without form is not implemented yet!');
    }

    private function entities(): array
    {
        return MakeHelper::findFilesInDirectory($this->bundleConfiguration->directories->entity);
    }

    private function formTypes(): array
    {
        return MakeHelper::findFilesInDirectory($this->bundleConfiguration->directories->form);
    }

    private function vos(): array
    {
        return MakeHelper::findFilesInDirectory($this->bundleConfiguration->directories->vo);
    }

    protected function configurations(string $namespace): array
    {
        if ($this->useAForm) {
            $configurations[] = (new MakerConfig(
                namespace: $namespace,
                builder: ControllerBuilder::class,
                classnameSuffix: 'Controller',
                namespacePrefix: $this->configNamespaces->controller,
            ))
                ->withTemplatePathKeepingNamespace('Controller/WithFormController.php')
                ->withExtraProperty('entityPath', $this->controllerRelatedEntity)
                ->withExtraProperty('formTypePath', $this->controllerRelatedFormType)
                ->withExtraProperty('voPath', $this->controllerRelatedVO);
        } else {
            $configurations[] = (new MakerConfig(
                namespace: $namespace,
                builder: ControllerBuilder::class,
                classnameSuffix: 'Controller',
                namespacePrefix: $this->configNamespaces->controller,
            ))
                ->withTemplatePathKeepingNamespace('Controller/SimpleController.php')
                ->withExtraProperty('entityPath', $this->controllerRelatedEntity);
        }

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
