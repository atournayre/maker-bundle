<?php

namespace Atournayre\Bundle\MakerBundle\Command;

use Atournayre\Bundle\MakerBundle\Command\MakeTrait\ClassTraitAdder;
use Atournayre\Bundle\MakerBundle\Command\MakeTrait\TraitForCollectionGenerator;
use Atournayre\Bundle\MakerBundle\Command\MakeTrait\TraitGenerator;
use Exception;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\MakerInterface;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class MakeTrait extends AbstractMaker implements MakerInterface
{
    private TraitGenerator              $traitGenerator;
    private TraitForCollectionGenerator $traitForCollectionGenerator;
    private ClassTraitAdder             $classTraitAdder;

    /**
     * @param TraitGenerator              $traitGenerator
     * @param TraitForCollectionGenerator $traitForCollectionGenerator
     * @param ClassTraitAdder             $classTraitAdder
     */
    public function __construct(
        TraitGenerator $traitGenerator,
        TraitForCollectionGenerator $traitForCollectionGenerator,
        ClassTraitAdder $classTraitAdder
    )
    {
        $this->traitGenerator = $traitGenerator;
        $this->traitForCollectionGenerator = $traitForCollectionGenerator;
        $this->classTraitAdder = $classTraitAdder;
    }

    /**
     * @return string
     */
    public static function getCommandName(): string
    {
        return 'make:trait';
    }

    /**
     * @return string
     */
    public static function getCommandDescription(): string
    {
        return 'Creates a Trait for a Doctrine entity class or simple class.';
    }

    /**
     * @param Command            $command
     * @param InputConfiguration $inputConfig
     *
     * @return void
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->addArgument('name',
                          InputArgument::OPTIONAL,
                          sprintf('Namespace of the entity/class represented by the trait to create (e.g. <fg=yellow>App\\Entity\\%s</>)',
                                  Str::asClassName(Str::getRandomTerm())
                          )
            )
            ->addArgument('isCollection', InputArgument::OPTIONAL, 'Do the trait represent a collection (y/n) ?')
            ->addOption('regenerate', null, InputOption::VALUE_NONE, 'Overwrite existing Trait.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param ConsoleStyle   $io
     * @param Generator      $generator
     *
     * @return void
     * @throws Exception
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $entityNamespace = $input->getArgument('name');
        $isCollection = $input->getArgument('isCollection') === 'y';
        $regenerate = $input->getOption('regenerate');

        if ($isCollection) {
            $classNameDetails = $this->traitForCollectionGenerator->generate($entityNamespace, $regenerate);
        }

        if (!$isCollection) {
            $classNameDetails = $this->traitGenerator->generate($entityNamespace, $regenerate);
        }

        $isFirstField = true;
        while (true) {
            $objectNamespace = $this->askForNamespacesOfClassesUsingTrait($io, $isFirstField);
            $isFirstField = false;

            if (null === $objectNamespace) {
                break;
            }

            $this->classTraitAdder->generate($objectNamespace, $classNameDetails->getFullName());
        }

        $this->writeSuccessMessage($io);
    }

    /**
     * @param DependencyBuilder   $dependencies
     * @param InputInterface|null $input
     *
     * @return void
     */
    public function configureDependencies(DependencyBuilder $dependencies, InputInterface $input = null)
    {
        // TODO: Implement configureDependencies() method.
    }

    /**
     * @param ConsoleStyle $io
     * @param bool         $isFirstField
     *
     * @return string|null
     */
    private function askForNamespacesOfClassesUsingTrait(ConsoleStyle $io, bool $isFirstField): ?string
    {
        $io->writeln('');

        if ($isFirstField) {
            $questionText = 'New class namespace (press <return> to stop adding trait to classes)';
        } else {
            $questionText = 'Add another class namespace? Enter the class namespace (or press <return> to stop adding trait to classes)';
        }

        return $io->ask($questionText);
    }
}
