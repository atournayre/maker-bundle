<?php

/**
 * ONLY
 * - primitive types : string, int, float, bool, array, \DateTimeInterface or VO
 *
 * MUST
 * - check validity of the data on creation
 * - be immutable
 * - be final
 *
 * SHOULD
 * - have a named constructor
 * - have withers
 * - have logic
 *
 * MUST NOT
 * - have setters
 *
 * @object-type VO
 */

declare(strict_types=1);

namespace App\VO;

use App\Contracts\Null\NullableInterface;
use App\Trait\IsTrait;
use App\Trait\NotNullableTrait;
use App\Type\Primitive\StringType;
use Webmozart\Assert\Assert;

final class Dummy implements NullableInterface
{
    use IsTrait;
    use NotNullableTrait;

    private function __construct(
        private \DateTimeInterface $createdAt,
        private StringType $fixtureVo,
        private int $id,
    ) {
    }

    public static function create(\DateTimeInterface $createdAt, StringType $fixtureVo, int $id): self
    {
        // Add assertions

        return new self($createdAt, $fixtureVo, $id);
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getFixtureVo(): StringType
    {
        return $this->fixtureVo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function withCreatedAt(\DateTimeInterface $createdAt): self
    {
        $clone = clone $this;
        $clone->createdAt = $createdAt;
        return $clone;
    }

    public function withFixtureVo(StringType $fixtureVo): self
    {
        $clone = clone $this;
        $clone->fixtureVo = $fixtureVo;
        return $clone;
    }

    public function withId(int $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }
}
