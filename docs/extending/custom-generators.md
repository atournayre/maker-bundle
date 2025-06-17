# Creating Custom Generators

This guide explains how to create your own custom generators for the Atournayre Maker Bundle.

## Overview

Custom generators allow you to extend the bundle with your own code generation capabilities. Each generator is a class that implements the `MakerInterface` or extends the `AbstractMaker` class.

## Step 1: Create a New Maker Class

Create a new class that extends `AbstractMaker`:

```php
<?php

namespace App\Maker;

use Atournayre\Bundle\MakerBundle\Generator\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

class CustomMaker extends AbstractMaker
{
    public function __construct(
        Environment $twig,
        string $namespacePrefix = 'App',
        string $dirPrefix = 'src'
    ) {
        parent::__construct($twig, $namespacePrefix, $dirPrefix);
    }

    public function getCommandName(): string
    {
        return 'make:atournayre:custom';
    }

    public function getCommandDescription(): string
    {
        return 'Creates a custom component';
    }

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the component')
            ->addOption('option1', null, InputOption::VALUE_OPTIONAL, 'Description of option1')
        ;
    }

    protected function doGenerate(InputInterface $input, SymfonyStyle $io): void
    {
        $name = $input->getArgument('name');
        $option1 = $input->getOption('option1');
        
        // Generate your code here
        $targetPath = $this->getPath() . '/' . $name . '.php';
        
        $this->generateFile($targetPath, 'custom/template.twig', [
            'name' => $name,
            'option1' => $option1,
            // Add more parameters as needed
        ]);
        
        $io->success("Custom component {$name} generated successfully!");
    }
}
```

## Step 2: Create a Template

Create a Twig template for your generator in `templates/custom/template.twig`:

```twig
<?php

namespace {{ namespace }};

/**
 * {{ name }} class.
 */
class {{ name }}
{
    {% if option1 %}
    private string $option1 = '{{ option1 }}';
    
    public function getOption1(): string
    {
        return $this->option1;
    }
    {% endif %}
}
```

## Step 3: Register Your Maker

### Option 1: Using Service Configuration

Register your maker as a service with the `atournayre.maker` tag:

```yaml
# config/services.yaml
services:
    App\Maker\CustomMaker:
        arguments:
            $twig: '@twig'
            $namespacePrefix: '%atournayre_maker.namespace_prefix%'
            $dirPrefix: '%atournayre_maker.dir_prefix%'
        tags: ['atournayre.maker']
```

### Option 2: Using Autoconfiguration

If you've set up autoconfiguration in your application, the bundle will automatically discover and register your maker if it implements `MakerInterface`.

## Step 4: Test Your Generator

Run your custom generator:

```bash
php bin/console make:atournayre:custom MyComponent --option1="value"
```

## Best Practices

1. **Follow Naming Conventions**: Use the prefix `make:atournayre:` for your command names.
2. **Provide Clear Documentation**: Add detailed descriptions to your command arguments and options.
3. **Use Input Validation**: Validate user input before generating code.
4. **Organize Templates**: Keep your templates organized in subdirectories based on the type of code they generate.
5. **Reuse Existing Templates**: Extend or include existing templates when possible.

## Advanced Customization

For more advanced customization, you can implement the `MakerInterface` directly instead of extending `AbstractMaker`. This gives you complete control over the generation process.

```php
<?php

namespace App\Maker;

use Atournayre\Bundle\MakerBundle\Generator\MakerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AdvancedMaker implements MakerInterface
{
    // Implement all required methods
}
```

## Next Steps

- Learn about the [API Reference](../api-reference.md) for more details on available classes and interfaces
- Explore the [source code](https://github.com/atournayre/maker-bundle) for examples of built-in generators
