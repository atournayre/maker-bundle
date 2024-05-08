<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Atournayre\Bundle\MakerBundle\Traits\Config\PropertiesAllowedTypesTrait;
use function Symfony\Component\String\u;

class ListenerMakerConfiguration extends MakerConfiguration
{
    use PropertiesAllowedTypesTrait;

    private string $eventNamespace = '';

    public static function fromFqcn(string $rootDir, string $rootNamespace, string $fqcn,): static
    {
        $fqcn = u($fqcn)->ensureEnd('Listener')->toString();

        return self::fromFqcn($rootDir, $rootNamespace, $fqcn);
    }

    public function eventNamespace(): string
    {
        return $this->eventNamespace;
    }

    public function withEventNamespace(string $eventNamespace): self
    {
        $config = clone $this;
        $config->eventNamespace = $eventNamespace;
        return $config;
    }
}
