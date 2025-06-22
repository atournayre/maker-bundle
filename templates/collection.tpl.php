<?php
declare(strict_types=1);

namespace {{ namespace }};

use Atournayre\Common\Assert\Assert;
use Atournayre\Contracts\Collection\AsListInterface;
use Atournayre\Contracts\Collection\AsMapInterface;
use Atournayre\Contracts\Exception\ThrowableInterface;
use Atournayre\Primitives\Collection;
use Atournayre\Primitives\Traits\Collection as Collection_;

final class {{ collection_name }} implements {{ interfaces|join(', ') }}
{
    use Collection_;

{% if 'AsListInterface' in interfaces %}
    public static function asList(array $collection): self
    {
        Assert::isListOf($collection, {{ class_name }}::class);

        return new self(Collection::of($collection));
    }
{% endif %}

{% if 'AsMapInterface' in interfaces %}
    public static function asMap(array $collection): self
    {
        Assert::isMapOf($collection, {{ class_name }}::class);

        return new self(Collection::of($collection));
    }
{% endif %}
}
