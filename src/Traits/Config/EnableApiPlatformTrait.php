<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Traits\Config;

trait EnableApiPlatformTrait
{
    private bool $enableApiPlatform = false;

    public function enableApiPlatform(): bool
    {
        return $this->enableApiPlatform;
    }

    public function withEnableApiPlatform(bool $enableApiPlatform): self
    {
        $config = clone $this;
        $config->enableApiPlatform = $enableApiPlatform;
        return $config;
    }
}
