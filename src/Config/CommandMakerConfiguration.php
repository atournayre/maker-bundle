<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use function Symfony\Component\String\u;

class CommandMakerConfiguration extends MakerConfiguration
{
    private string $title = '';
    private string $description = '';
    private string $commandName = '';

    public static function fromFqcn(string $rootDir, string $rootNamespace, string $fqcn,): static
    {
        $fqcn = u($fqcn)->ensureEnd('Command')->toString();

        return self::fromFqcn($rootDir, $rootNamespace, $fqcn);
    }

    public function title(): string
    {
        return $this->title;
    }

    private function sanitizeTitle(string $title): string
    {
        return addslashes($title);
    }

    public function withTitle(string $title): self
    {
        $config = clone $this;
        $config->title = $this->sanitizeTitle($title);
        return $config;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function withDescription(string $description): self
    {
        $config = clone $this;
        $config->description = $description;
        return $config;
    }

    public function commandName(): string
    {
        return $this->commandName;
    }

    private function sanitizeCommandName(string $commandName): string
    {
        return u($commandName)
            ->trim()
            ->lower()
            ->ensureStart('app:')
            ->toString();
    }

    public function withCommandName(string $commandName): self
    {
        $config = clone $this;
        $config->commandName = $this->sanitizeCommandName($commandName);
        return $config;
    }
}
