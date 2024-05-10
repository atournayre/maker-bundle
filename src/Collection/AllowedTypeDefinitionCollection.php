<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Collection;

use Atournayre\Bundle\MakerBundle\DTO\AllowedTypeDefinition;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Collection\TypedCollectionImmutable;
use Webmozart\Assert\Assert;

/**
 * @extends TypedCollectionImmutable<AllowedTypeDefinition>
 *
 * @method AllowedTypeDefinitionCollection add(AllowedTypeDefinition $value)
 * @method AllowedTypeDefinition[] values()
 * @method AllowedTypeDefinition first()
 * @method AllowedTypeDefinition last()
 * @method AllowedTypeDefinition offsetGet(mixed $offset)
 * @method AllowedTypeDefinitionCollection offsetSet(mixed $offset, AllowedTypeDefinition $value)
 */
final class AllowedTypeDefinitionCollection extends TypedCollectionImmutable
{
    protected static string $type = AllowedTypeDefinition::class;

    public function typeExists(string|AllowedTypeDefinition $type): bool
    {
        $type = $type instanceof AllowedTypeDefinition ? $type->getType() : $type;

        return $this
            ->toMap()
            ->map(fn (AllowedTypeDefinition $allowedTypeDefinition) => $allowedTypeDefinition->getType())
            ->in($type)
        ;
    }

    public function asString(string $glue = ', '): string
    {
        return $this
            ->toMap()
            ->map(fn (AllowedTypeDefinition $allowedTypeDefinition) => $allowedTypeDefinition->getType())
            ->join($glue)
        ;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function assertTypeExists(string|AllowedTypeDefinition $type, string $path): void
    {
        Assert::true(
            $this->typeExists($type),
            Str::sprintf('Property "%s" should be of type %s, %s given', $path, $this->asString(), $type)
        );
    }
}
