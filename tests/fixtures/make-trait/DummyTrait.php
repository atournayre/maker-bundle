<?php

/**
 * This file has been auto-generated
 */

declare(strict_types=1);

namespace App\Trait;

use App\Type\Primitive\StringType;
use DateTimeInterface;

trait DummyTrait
{
    private DateTimeInterface $createdAt;
    private ?StringType $fixtureVo = null;
    private int $id;

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getFixtureVo(): ?StringType
    {
        return $this->fixtureVo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function withCreatedAt(DateTimeInterface $createdAt): self
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
