<?php
declare(strict_types=1);

namespace App\Type\Primitive;

class ListImmutableType extends ListType
{
    public function offsetSet($offset, $value): void
    {
        throw new \RuntimeException('Collection is immutable');
    }

    public function offsetUnset($offset): void
    {
        throw new \RuntimeException('Collection is immutable');
    }
}
