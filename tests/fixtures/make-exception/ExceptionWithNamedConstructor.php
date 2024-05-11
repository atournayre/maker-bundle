<?php

/**
 * This file has been auto-generated
 */

declare(strict_types=1);

namespace App\Exception;

final class Dummy extends \Exception
{
    public static function itFails(): Dummy
    {
        return new Dummy('Oops, an error occured.');
    }
}
