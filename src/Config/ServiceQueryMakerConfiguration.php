<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

class ServiceQueryMakerConfiguration extends MakerConfiguration
{
    private string $vo = '';

    protected static function classNameSuffix(): string
    {
        return 'QueryService';
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
