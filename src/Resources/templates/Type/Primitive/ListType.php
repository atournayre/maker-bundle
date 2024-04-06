<?php
declare(strict_types=1);

namespace App\Type\Primitive;

use Webmozart\Assert\Assert;

class ListType extends AbstractCollectionType
{
    private function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public static function create(array $collection): self
    {
        Assert::isList($collection, 'Collection must be a list.');

        return new static($collection);
    }
}
