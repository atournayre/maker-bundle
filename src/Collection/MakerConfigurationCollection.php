<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Collection;

use Aimeos\Map;
use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
use Atournayre\Collection\TypedCollection;

class MakerConfigurationCollection extends TypedCollection
{
    protected static string $type = MakerConfigurationInterface::class;

    /**
     * @param array<MakerConfigurationInterface> $collection
     */
    public static function createAsList(array $collection): static
    {
        return new static($collection);
    }

    /**
     * @param array<MakerConfigurationInterface> $collection
     */
    public static function createAsMap(array $collection): static
    {
        return new static($collection);
    }

    public function absolutePaths(): Map
    {
        return $this
            ->toMap()
            ->map(fn(MakerConfigurationInterface $makerConfiguration) => $makerConfiguration->absolutePath());
    }
}
