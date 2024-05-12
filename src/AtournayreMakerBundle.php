<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html
 */
class AtournayreMakerBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
