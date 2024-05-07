<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use function Symfony\Component\String\u;

class ServiceCommandMakerConfiguration extends MakerConfiguration
{
    private string $vo = '';

    public static function fromFqcn(string $rootDir, string $rootNamespace, string $fqcn,): static
    {
        $fqcn = u($fqcn)->ensureEnd('CommandService')->toString();

        return parent::fromFqcn($rootDir, $rootNamespace, $fqcn);
    }

    public function vo(): string
    {
        return $this->vo;
    }

    public function withVo(string $vo): self
    {
        $config = clone $this;
        $config->vo = $vo;
        return $config;
    }
}
