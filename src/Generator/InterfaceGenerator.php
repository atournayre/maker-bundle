<?php

namespace Atournayre\Bundle\MakerBundle\Generator;

use Symfony\Bundle\MakerBundle\Generator;

class InterfaceGenerator
{
    public function __construct(
        private readonly Generator $generator,
        private readonly string    $rootDir,
        private readonly string    $skeletonDir,
        private readonly string    $rootNamespace,
    )
    {
    }

    public function generate(string $namespacePath, string $name): void
    {
        $path = new NamespacePath($namespacePath, $this->rootNamespace);
        $name = NamespacePath::normalize($name);

        $this->generateInterface($path, $name);

        $this->generator->writeChanges();
    }

    public function generateInterface(NamespacePath $path, string $name): void
    {
        $className = ucfirst($name).'Interface';
        $targetPath = $this->rootDir . '/Contracts/' . $path->normalizedValue() . '/' . $className . '.php';
        $templateName = $this->skeletonDir . '/src/Contracts/Interface.tpl.php';
        $variables = [
            'root_namespace' => $this->rootNamespace,
            'namespace' => $path->toNamespace('\\Contracts' . $path->normalizedValue()),
            'class_name' => $className,
        ];

        $this->generator->generateFile($targetPath, $templateName, $variables);
    }
}
