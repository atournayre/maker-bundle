<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Contracts;

interface MakerConfigurationInterface
{
    /**
     * @throws \Throwable
     */
    public static function fromNamespace(
        string $rootDir,
        string $rootNamespace,
        string $namespace,
        string $className,
    ): static;

    /**
     * @throws \Throwable
     */
    public static function fromFqcn(
        string $rootDir,
        string $rootNamespace,
        string $fqcn,
    ): self;

    /**
     * @throws \Throwable
     */
    public static function fromTemplate(
        string $rootDir,
        string $rootNamespace,
        string $templatePath,
    ): static;

    public function namespace(): string;

    public function classname(): string;

    public function fqcn(): string;

    public function rootDir(): string;

    public function rootNamespace(): string;

    public function withSourceCode(string $sourceCode): self;

    public function absolutePath(): string;

    public function sourceCode(): string;

    /**
     * @return array<string>
     */
    public function allowedTypes(): array;

    /**
     * @return array<string>
     */
    public function propertiesAllowedTypes(): array;
}
