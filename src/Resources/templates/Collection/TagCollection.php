<?php
declare(strict_types=1);

namespace App\Collection;

use Atournayre\Collection\TypedCollection;
use Webmozart\Assert\Assert;

/**
 * @extends TypedCollection<string>
 *
 * @method SplFileInfoCollection add(string $value)
 * @method string[] values()
 * @method string first()
 * @method string last()
 */
final class TagCollection extends TypedCollection
{
    private const TAG_MIN_LENGTH = 3;
    private const TAG_MAX_LENGTH = 5;

    public function validateElement(mixed $value): void
    {
        Assert::lengthBetween(
            $value,
            self::TAG_MIN_LENGTH,
            self::TAG_MAX_LENGTH,
            sprintf('Tag "%s" length must be between %d and %d characters', $value, self::TAG_MIN_LENGTH, self::TAG_MAX_LENGTH)
        );
    }
}
