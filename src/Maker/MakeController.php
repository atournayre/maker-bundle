<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Config\ControllerMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Finder\SplFileInfo;

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
            ->addArgument('namespace', InputArgument::REQUIRED, 'The class name of the Controller <fg=yellow>(e.g. DummyController)</>');
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Controller';
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

        if (empty($this->entities())) {
            $io->error('No entity found in the Entity directory');
            return;
        }

        $questionControllerRelatedEntity = new ChoiceQuestion('Choose the entity related to this Controller', $this->entities());
        $this->controllerRelatedEntity = $io->askQuestion($questionControllerRelatedEntity);

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

    /**
     * @return string[]
     */
    private function entities(): array
    {
        return $this->filesystem->findFilesInDirectory($this->bundleConfiguration->directories->entity);
    }

    /**
     * @return string[]
     */
    private function formTypes(): array
    {
        return $this->filesystem->findFilesInDirectory($this->bundleConfiguration->directories->form);
    }

    /**
     * @return string[]
     */
    private function vos(): array
    {
        return $this->filesystem->findFilesInDirectory($this->bundleConfiguration->directories->vo);
    }

    /**
     * @param string $namespace
     * @return MakerConfigurationCollection
     * @throws \Throwable
     */
    protected function configurations(string $namespace): MakerConfigurationCollection
    {
        $withFormControllerPath = $this->bundleConfiguration->directories->controller.'/WithFormController.php';
        $withFormController = new SplFileInfo($withFormControllerPath, $withFormControllerPath, $withFormControllerPath);

        $fqcn = Str::sprintf('%s\%s', $this->bundleConfiguration->namespaces->controller, $namespace);

        return MakerConfigurationCollection::createAsList([
            ControllerMakerConfiguration::fromFqcn(
                rootDir: $this->rootDir,
                rootNamespace: $this->rootNamespace,
                fqcn: $fqcn,
            )
                ->withSourceCode($withFormController->getContents())
                ->withEntityPath($this->controllerRelatedEntity)
                ->withFormTypePath($this->controllerRelatedFormType)
                ->withVoPath($this->controllerRelatedVO),
        ]);
    }

    public function dependencies(): array
    {
        return [
            \Symfony\Component\Form\Extension\Core\Type\FormType::class => 'symfony/form',
            \Symfony\Component\Form\FormInterface::class => 'symfony/form',
        ];
    }
}
