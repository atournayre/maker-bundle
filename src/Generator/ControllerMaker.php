<?php

declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Twig\Environment;

/**
 * Maker for generating controller classes.
 */
final class ControllerMaker extends AbstractMaker implements MakerInterface
{
    private const DEFAULT_CONTROLLER_NAMESPACE = 'Controller';
    private const DEFAULT_CONTROLLER_DIRECTORY = 'src/Controller';

    public function __construct(
        Environment $twig,
        string $namespacePrefix = 'App',
        string $dirPrefix = 'src',
        private readonly ?string $controllerNamespace = null,
        private readonly ?string $controllerDirectory = null,
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
        ;
    }

    protected function doGenerate(InputInterface $input, SymfonyStyle $io): void
    {
        // 1. Get the controller name (from argument or ask interactively)
        $controllerName = $input->getArgument('name');
        if (empty($controllerName)) {
            $controllerName = $this->askForControllerName($io);
        } elseif (str_ends_with($controllerName, 'Controller')) {
            $controllerName = substr($controllerName, 0, -10); // Remove 'Controller' suffix
        }

        // 2. Ask if the controller should extend AbstractController
        $extendsAbstractController = $input->getOption('extends-abstract-controller');
        if (!$input->getOption('extends-abstract-controller') && !$input->isInteractive()) {
            $extendsAbstractController = false;
        } elseif (!$input->getOption('extends-abstract-controller')) {
            $extendsAbstractController = $this->askForExtendsAbstractController($io);
        }

        // 3. Determine namespace and directory
        $customNamespace = $input->getOption('namespace');
        $namespace = !empty($customNamespace) 
            ? $this->namespace($customNamespace) 
            : $this->getControllerNamespace();
        $directory = $this->getControllerDirectory();

        // 4. Generate route pattern and name
        $routePattern = $this->generateRoutePattern($controllerName);
        $routeName = $this->generateRouteName($controllerName);
        $templatePath = $this->generateTemplatePath($controllerName);

        // 5. Confirm namespace and directory
        $io->section('Summary');
        $io->table(
            ['Property', 'Value'],
            [
                ['Controller name', $controllerName . 'Controller'],
                ['Extends AbstractController', $extendsAbstractController ? 'Yes' : 'No'],
                ['Namespace', $namespace],
                ['Directory', $directory],
                ['File path', $directory . '/' . $controllerName . 'Controller.php'],
                ['Route pattern', $routePattern],
                ['Route name', $routeName],
                ['Template path', $templatePath],
            ]
        );

        if (!$io->confirm('Do you want to generate this controller class?', true)) {
            $io->warning('Controller generation cancelled.');
            return;
        }

        // 6. Generate the controller class
        $this->generateControllerClass(
            $controllerName,
            $extendsAbstractController,
            $namespace,
            $directory,
            $routePattern,
            $routeName,
            $templatePath
        );

        $io->success([
            'Controller class generated successfully!',
            'Path: ' . $directory . '/' . $controllerName . 'Controller.php',
        ]);
    }

    private function askForControllerName(SymfonyStyle $io): string
    {
        $question = new Question('Choose controller name (without "Controller" suffix):');
        $question->setValidator(function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('The controller name cannot be empty.');
            }

            if (str_ends_with($answer, 'Controller')) {
                throw new \RuntimeException('Please provide the name without the "Controller" suffix.');
            }

            return $answer;
        });

        return $io->askQuestion($question);
    }

    private function askForExtendsAbstractController(SymfonyStyle $io): bool
    {
        $question = new ConfirmationQuestion(
            'Should the controller extend Symfony\Bundle\FrameworkBundle\Controller\AbstractController?',
            false
        );

        return $io->askQuestion($question);
    }

    private function getControllerNamespace(): string
    {
        return $this->namespace($this->controllerNamespace ?? self::DEFAULT_CONTROLLER_NAMESPACE);
    }

    private function getControllerDirectory(): string
    {
        return $this->controllerDirectory ?? self::DEFAULT_CONTROLLER_DIRECTORY;
    }

    private function generateRoutePattern(string $controllerName): string
    {
        // Convert camel case to kebab case
        $pattern = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $controllerName));
        return '/' . strtolower($pattern);
    }

    private function generateRouteName(string $controllerName): string
    {
        // Convert camel case to snake case
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $controllerName));
    }

    private function generateTemplatePath(string $controllerName): string
    {
        // Convert camel case to snake case for the template path
        $templateName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $controllerName));
        return $templateName . '/index.html.twig';
    }

    private function generateControllerClass(
        string $controllerName,
        bool $extendsAbstractController,
        string $namespace,
        string $directory,
        string $routePattern,
        string $routeName,
        string $templatePath
    ): void {
        $className = $controllerName . 'Controller';
        $filePath = $directory . '/' . $className . '.php';

        $this->generateFile($filePath, 'controller.tpl.php', [
            'namespace' => $namespace,
            'class_name' => $controllerName,
            'extends_abstract_controller' => $extendsAbstractController,
            'route_pattern' => $routePattern,
            'route_name' => $routeName,
            'template_path' => $templatePath,
        ]);
    }
}
