<?php

/*
 * This file is part of the Atournayre MakerBundle package.
 *
 * (c) Aurélien Tournayre <aurelien.tournayre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atournayre\Bundle\MakerBundle;

use Atournayre\Bundle\MakerBundle\DependencyInjection\MakerExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Aurélien Tournayre <aurelien.tournayre@gmail.com>
 */
class MakerBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new MakerExtension();
        }

        return $this->extension;
    }
}
