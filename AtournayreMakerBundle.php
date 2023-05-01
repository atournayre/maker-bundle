<?php

namespace Atournayre\Bundle\MakerBundle;

use Atournayre\Bundle\MakerBundle\DependencyInjection\MakerExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AtournayreMakerBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new MakerExtension();
        }
        return $this->extension;
    }
}
