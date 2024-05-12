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

namespace App\VO\Entity;

use App\Contracts\Null\NullableInterface;
use App\Trait\IsTrait;
use App\Trait\NotNullableTrait;
use App\Type\Primitive\StringType;
use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureEntity;
use DateTimeInterface;
use Webmozart\Assert\Assert;

final class Dummy implements NullableInterface
{
    use IsTrait;
    use NotNullableTrait;

    private DateTimeInterface $createdAt;
    private StringType $fixtureVo;
    private int $id;

    public static function create(FixtureEntity $fixtureEntity): self
    {
        // Add assertions here if needed
        $self = new self();
        // $self->createdAt = $fixtureEntity->getCreatedAt();
        // $self->fixtureVo = $fixtureEntity->getFixtureVo();
        // $self->id = $fixtureEntity->getId();
        return $self;
    }

    public function getCreatedAt(): DateTimeInterface
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
}
