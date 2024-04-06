<?php
declare(strict_types=1);

namespace App\Type\Primitive;

use Webmozart\Assert\Assert;

class MapType extends AbstractCollectionType
{
    private function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public static function create(array $collection): self
    {
        Assert::isMap($collection, 'Collection must be a map.');

        return new static($collection);
    }
}
