<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use function Symfony\Component\String\u;

class ServiceQueryMakerConfiguration extends MakerConfiguration
{
    private string $vo = '';

    public static function fromFqcn(string $rootDir, string $rootNamespace, string $fqcn,): static
    {
        $fqcn = u($fqcn)->ensureEnd('QueryService')->toString();

        return self::fromFqcn($rootDir, $rootNamespace, $fqcn);
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
