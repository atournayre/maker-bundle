<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Contracts;

use Atournayre\Bundle\MakerBundle\Config\MakerConfiguration;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;

interface PhpFileBuilderInterface
{
    public function createPhpFileDefinition(MakerConfiguration|MakerConfigurationInterface $makerConfiguration): PhpFileDefinition;

    public function supports(string $makerConfigurationClassName): bool;
}
