<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Collection;

use Aimeos\Map;
use Atournayre\Bundle\MakerBundle\Config\MakerConfiguration;
use Atournayre\Collection\TypedCollection;

class MakerConfigurationCollection extends TypedCollection
{
    protected static string $type = MakerConfiguration::class;

    public function filterByBuilder(string $builder): self
    {
        $collection = $this
            ->toMap()
            ->filter(fn(MakerConfiguration $makerConfiguration) => $makerConfiguration->supportsBuilder($builder));

        return self::fromMapAsList($collection);
    }

    public function absolutePaths(): Map
    {
        return $this
            ->toMap()
            ->map(fn(MakerConfiguration $makerConfiguration) => $makerConfiguration->absolutePath());
    }
}
