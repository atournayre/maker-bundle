# API Reference

This page provides a reference for the main classes and interfaces in the Atournayre Maker Bundle.

## Core Interfaces

### MakerInterface

The `MakerInterface` is the core interface that all makers must implement.

```php
namespace Atournayre\Bundle\MakerBundle\Generator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface MakerInterface
{
    /**
     * Return the command name (e.g. make:atournayre:entity).
     */
    public function getCommandName(): string;
    
    /**
     * Return the command description.
     */
    public function getCommandDescription(): string;
    
    /**
     * Configure the command (add arguments, options, etc.).
     */
    public function configureCommand(Command $command): void;
    
    /**
     * Generate the code.
     * 
     * @return int The command exit code
     */
    public function generate(InputInterface $input, OutputInterface $output): int;
}
```

## Abstract Classes

### AbstractMaker

The `AbstractMaker` class provides a base implementation of the `MakerInterface` with common functionality.

```php
namespace Atournayre\Bundle\MakerBundle\Generator;

abstract class AbstractMaker implements MakerInterface
{
    protected Environment $twig;
    protected string $namespacePrefix;
    protected string $dirPrefix;

    public function __construct(
        Environment $twig,
        string $namespacePrefix = 'App',
        string $dirPrefix = 'src'
    );

    /**
     * {@inheritdoc}
     */
    public function generate(InputInterface $input, OutputInterface $output): int;
    
    /**
     * Perform the actual code generation.
     */
    abstract protected function doGenerate(InputInterface $input, SymfonyStyle $io): void;
    
    /**
     * Generate a file from a Twig template.
     */
    protected function generateFile(string $targetPath, string $template, array $parameters = []): void;
    
    /**
     * Get the full namespace for a class.
     */
    protected function getNamespace(string $subNamespace = ''): string;
    
    /**
     * Get the full path for a file.
     */
    protected function getPath(string $subPath = ''): string;
}
```

## Command Classes

### MakerCommand

The `MakerCommand` class is a Symfony console command that delegates to a maker service.

```php
namespace Atournayre\Bundle\MakerBundle\Command;

use Atournayre\Bundle\MakerBundle\Generator\MakerInterface;
use Symfony\Component\Console\Command\Command;

class MakerCommand extends Command
{
    private MakerInterface $maker;
    
    public function __construct(MakerInterface $maker);
    
    protected function configure(): void;
    
    protected function execute(InputInterface $input, OutputInterface $output): int;
}
```

### CommandFactory

The `CommandFactory` class creates command instances for maker services.

```php
namespace Atournayre\Bundle\MakerBundle\Command;

use Atournayre\Bundle\MakerBundle\Generator\MakerInterface;

class CommandFactory
{
    /**
     * @var iterable<MakerInterface>
     */
    private iterable $makers;
    
    /**
     * @param iterable<MakerInterface> $makers
     */
    public function __construct(iterable $makers);
    
    /**
     * Create a command for each maker service.
     *
     * @return array<MakerCommand>
     */
    public function createCommands(): array;
    
    /**
     * Create a command for a specific maker service.
     */
    public function createCommand(MakerInterface $maker): MakerCommand;
}
```

## Template Classes

### TemplateRenderer

The `TemplateRenderer` class provides methods for rendering Twig templates.

```php
namespace Atournayre\Bundle\MakerBundle\Template;

use Twig\Environment;

class TemplateRenderer
{
    private Environment $twig;
    
    public function __construct(Environment $twig);
    
    /**
     * Render a template with the given parameters.
     */
    public function render(string $template, array $parameters = []): string;
    
    /**
     * Generate a file from a template.
     */
    public function generateFile(string $targetPath, string $template, array $parameters = []): void;
}
```

## Bundle Configuration

### Configuration

The `Configuration` class defines the configuration options for the bundle.

```php
namespace Atournayre\Bundle\MakerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder;
}
```

### AtournayeMakerExtension

The `AtournayeMakerExtension` class loads and manages the bundle configuration.

```php
namespace Atournayre\Bundle\MakerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AtournayeMakerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void;
}
```

## Compiler Passes

### MakerCompilerPass

The `MakerCompilerPass` class automatically tags services that implement `MakerInterface`.

```php
namespace Atournayre\Bundle\MakerBundle\DependencyInjection\CompilerPass;

use Atournayre\Bundle\MakerBundle\Generator\MakerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MakerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void;
}
```
