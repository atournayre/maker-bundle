<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Traits;

use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;

trait PropertiesTrait
{
    /**
     * @var array|PropertyDefinition[] $properties
     */
    private array $properties = [];
}
