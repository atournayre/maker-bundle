<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html
 */
class AtournayreMakerBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
