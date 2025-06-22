<?php

declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Primitives\Collection;
use Atournayre\Primitives\StringType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

/**
 * Maker for generating controller classes.
 */
final class ControllerMaker extends AbstractMaker implements MakerInterface
{
    private const DEFAULT_CONTROLLER_NAMESPACE = 'Controller';

    private const DEFAULT_CONTROLLER_DIRECTORY = 'src/Controller';

    /**
     * @param array<int, string> $controllerInterfaces List of interface class names to be implemented by generated controllers
     */
    public function __construct(
        Environment $twig,
        string $namespacePrefix = 'App',
        string $dirPrefix = 'src',
        private readonly ?string $controllerNamespace = null,
        private readonly ?string $controllerDirectory = null,
        private readonly ?string $controllerTemplatePath = null,
        private readonly array $controllerInterfaces = [],
    ) {
        parent::__construct($twig, $namespacePrefix, $dirPrefix);
    }

    public function commandName(): string
    {
        return 'make:elegant:controller';
    }

    public function commandDescription(): string
    {
        return 'Creates a new controller class';
    }

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the controller (without "Controller" suffix)')
            ->addOption('namespace', null, InputOption::VALUE_OPTIONAL, 'The namespace of the controller')
            ->addOption('extends-abstract-controller', null, InputOption::VALUE_NONE, 'Whether the controller should extend AbstractController')
            ->addOption('template', null, InputOption::VALUE_OPTIONAL, 'The template to use for generating the controller')
        ;
    }

    protected function doGenerate(InputInterface $input, SymfonyStyle $io): void
    {
        // 1. Get the controller name (from argument or ask interactively)
        $controllerName = $input->getArgument('name');
        if (null === $controllerName || '' === $controllerName) {
            $controllerName = $this->askForControllerName($io);
        } elseif (StringType::of($controllerName)->endsWith('Controller')->asBool()) {
            $controllerName = StringType::of($controllerName)->slice(0, -10)->toString(); // Remove 'Controller' suffix
        }

        // 2. Ask if the controller should extend AbstractController
        $extendsAbstractController = (bool) $input->getOption('extends-abstract-controller');
        if (false === $extendsAbstractController && !$input->isInteractive()) {
            $extendsAbstractController = false;
        } elseif (false === $extendsAbstractController) {
            $extendsAbstractController = $this->askForExtendsAbstractController($io);
        }

        // 3. Determine namespace and directory
        $customNamespace = $input->getOption('namespace');
        $namespace = (null === $customNamespace || '' === $customNamespace)
            ? $this->resolveControllerNamespace()
            : $this->namespace($customNamespace);
        $directory = $this->resolveControllerDirectory();

        // 4. Get template path from option or configuration
        $templatePath = $this->resolveControllerTemplatePath((string) $input->getOption('template'));

        // 5. Generate route pattern and name
        $routePattern = $this->generateRoutePattern($controllerName);
        $routeName = $this->generateRouteName($controllerName);
        $viewTemplatePath = $this->generateTemplatePath($controllerName);

        // 6. Confirm namespace and directory
        $io->section('Summary');
        $io->table(
            ['Property', 'Value'],
            [
                ['Controller name', $controllerName.'Controller'],
                ['Extends AbstractController', $extendsAbstractController ? 'Yes' : 'No'],
                ['Namespace', $namespace],
                ['Directory', $directory],
                ['File path', $directory.'/'.$controllerName.'Controller.php'],
                ['Route pattern', $routePattern],
                ['Route name', $routeName],
                ['View template path', $viewTemplatePath],
                ['Controller template', $templatePath],
                ['Interfaces', [] === $this->controllerInterfaces ? 'None' : implode(', ', $this->controllerInterfaces)],
            ]
        );

        if (!$io->confirm('Do you want to generate this controller class?', true)) {
            $io->warning('Controller generation cancelled.');

            return;
        }

        // 7. Generate the controller class
        $this->generateControllerClass(
            $controllerName,
            $extendsAbstractController,
            $namespace,
            $directory,
            $routePattern,
            $routeName,
            $viewTemplatePath,
            $templatePath
        );

        $io->success([
            'Controller class generated successfully!',
            'Path: '.$directory.'/'.$controllerName.'Controller.php',
        ]);
    }

    private function askForControllerName(SymfonyStyle $io): string
    {
        $question = $this->createQuestion('Choose controller name (without "Controller" suffix):');
        $question->setValidator(function ($answer) {
            if (null === $answer || '' === $answer) {
                throw $this->createRuntimeException('The controller name cannot be empty.');
            }

            if (StringType::of($answer)->endsWith('Controller')->asBool()) {
                throw $this->createRuntimeException('Please provide the name without the "Controller" suffix.');
            }

            return $answer;
        });

        return $io->askQuestion($question);
    }

    private function createQuestion(string $questionText): Question
    {
        return new Question($questionText);
    }

    private function createRuntimeException(string $message): \RuntimeException
    {
        return new \RuntimeException($message);
    }

    private function askForExtendsAbstractController(SymfonyStyle $io): bool
    {
        $question = $this->createConfirmationQuestion(
            'Should the controller extend Symfony\Bundle\FrameworkBundle\Controller\AbstractController?',
            false
        );

        return $io->askQuestion($question);
    }

    private function createConfirmationQuestion(string $questionText, bool $defaultAnswer): ConfirmationQuestion
    {
        return new ConfirmationQuestion($questionText, $defaultAnswer);
    }

    private function resolveControllerNamespace(): string
    {
        return $this->namespace($this->controllerNamespace ?? self::DEFAULT_CONTROLLER_NAMESPACE);
    }

    private function resolveControllerDirectory(): string
    {
        return $this->controllerDirectory ?? self::DEFAULT_CONTROLLER_DIRECTORY;
    }

    private function resolveControllerTemplatePath(string $optionTemplatePath = ''): string
    {
        // First check if a template path was provided as an option
        if ('' !== $optionTemplatePath && '0' !== $optionTemplatePath) {
            return $optionTemplatePath;
        }

        // Then check if a template path was configured
        if (null !== $this->controllerTemplatePath && '' !== $this->controllerTemplatePath && '0' !== $this->controllerTemplatePath) {
            return $this->controllerTemplatePath;
        }

        // Finally, fall back to the default template
        return 'controller.tpl.php';
    }

    private function generateRoutePattern(string $controllerName): string
    {
        // Convert camel case to kebab case
        $pattern = StringType::of(preg_replace('/(?<!^)[A-Z]/', '-$0', $controllerName))->lower()->toString();

        return '/'.StringType::of($pattern)->lower()->toString();
    }

    private function generateRouteName(string $controllerName): string
    {
        // Convert camel case to snake case
        return StringType::of(preg_replace('/(?<!^)[A-Z]/', '_$0', $controllerName))->lower()->toString();
    }

    private function generateTemplatePath(string $controllerName): string
    {
        // Convert camel case to snake case for the template path
        $templateName = StringType::of(preg_replace('/(?<!^)[A-Z]/', '_$0', $controllerName))->lower()->toString();

        return $templateName.'/index.html.twig';
    }

    private function generateControllerClass(
        string $controllerName,
        bool $extendsAbstractController,
        string $namespace,
        string $directory,
        string $routePattern,
        string $routeName,
        string $viewTemplatePath,
        string $controllerTemplatePath,
    ): void {
        $className = $controllerName.'Controller';
        $filePath = $directory.'/'.$className.'.php';

        // Generate the controller file using the template
        $this->generateFile($filePath, $controllerTemplatePath, [
            'namespace' => $namespace,
            'class_name' => $controllerName,
            'extends_abstract_controller' => $extendsAbstractController,
            'route_pattern' => $routePattern,
            'route_name' => $routeName,
            'template_path' => $viewTemplatePath,
        ]);

        // If there are interfaces, modify the generated file to add them
        if ([] !== $this->controllerInterfaces) {
            $content = file_get_contents($filePath);

            // Generate use statements for interfaces
            $useStatements = '';
            foreach ($this->controllerInterfaces as $interface) {
                $useStatements .= "use {$interface};\n";
            }

            // Generate implements clause
            $interfaceShortNames = Collection::of($this->controllerInterfaces)
                ->map(function ($interface) {
                    $parts = StringType::of($interface)->split('\\');
                    $collection = Collection::of($parts);

                    return $collection->last();
                })
                ->toArray()
            ;

            $implementsClause = ' implements '.implode(', ', $interfaceShortNames);

            // Add use statements after the last use statement
            $lastUsePos = strrpos($content, 'use ');
            $lastUseEndPos = strpos($content, "\n", $lastUsePos);
            $content = substr_replace($content, "\n".$useStatements, $lastUseEndPos, 0);

            // Add implements clause after the class declaration
            $classPos = strpos($content, 'final class '.$controllerName.'Controller');
            $extendsPos = strpos($content, ' extends ', $classPos);
            $implementsPos = false !== $extendsPos ? $extendsPos + StringType::of(' extends AbstractController')->length()->value() : strpos($content, "\n", $classPos);
            $content = substr_replace($content, $implementsClause, $implementsPos, 0);

            // Write the modified content back to the file
            file_put_contents($filePath, $content);
        }
    }
}
