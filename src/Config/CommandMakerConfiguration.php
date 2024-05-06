<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

class CommandMakerConfiguration extends MakerConfiguration
{
    private string $title = '';
    private string $description = '';
    private string $commandName = '';

    public function title(): string
    {
        return $this->title;
    }

    public function withTitle(string $title): self
    {
        $config = clone $this;
        $config->title = $title;
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

    public function withCommandName(string $commandName): self
    {
        $config = clone $this;
        $config->commandName = $commandName;
        return $config;
    }
}
