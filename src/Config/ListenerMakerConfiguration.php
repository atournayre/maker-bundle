<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Atournayre\Bundle\MakerBundle\Traits\Config\PropertiesAllowedTypesTrait;

class ListenerMakerConfiguration extends MakerConfiguration
{
    use PropertiesAllowedTypesTrait;

    private string $eventNamespace = '';

    protected static function classNameSuffix(): string
    {
        return 'Listener';
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
