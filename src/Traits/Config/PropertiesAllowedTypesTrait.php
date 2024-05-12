<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Traits\Config;

use Aimeos\Map;
use Atournayre\Bundle\MakerBundle\Collection\AllowedTypeDefinitionCollection;
use Atournayre\Bundle\MakerBundle\DTO\AllowedTypeDefinition;
use Webmozart\Assert\Assert;

trait PropertiesAllowedTypesTrait
{
    /**
     * @var array<string>
     */
    private array $propertiesAllowedTypes = [];

    /**
     * @param array<string> $propertiesAllowedTypes
     */
    public function withPropertiesAllowedTypes(array $propertiesAllowedTypes): self
    {
        $config = clone $this;
        $config->propertiesAllowedTypes = $propertiesAllowedTypes;
        return $config;
    }

    /**
     * @inheritDoc
     */
    public function correspondingTypes(): AllowedTypeDefinitionCollection
    {
        $correspondingTypes = Map::from($this->propertiesAllowedTypes)
            ->map(fn (string $allowedType): AllowedTypeDefinition => AllowedTypeDefinition::create($allowedType, $this->rootDir(), $this->rootNamespace()))
            ->toArray();

        $map = Map::from($correspondingTypes)
            ->combine($correspondingTypes)
            ->toArray();

        return AllowedTypeDefinitionCollection::createAsMap($map);
    }

    public function correspondingType(string $type): string
    {
        $correspondingTypes = $this->correspondingTypes();

        Assert::true($correspondingTypes->offsetExists($type), sprintf('Type "%s" is not allowed', $type));
        return $correspondingTypes->offsetGet($type)->getType();
    }
}
