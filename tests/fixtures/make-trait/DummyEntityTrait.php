<?php

/**
 * This file has been auto-generated
 */

declare(strict_types=1);

namespace App\Trait\Entity;

use App\Type\Primitive\StringType;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

trait DummyTrait
{
    #[ORM\Column(nullable: true, type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?StringType $fixtureVo = null;

    #[ORM\Column(nullable: true)]
    private ?int $id = null;

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getFixtureVo(): ?StringType
    {
        return $this->fixtureVo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setFixtureVo(?StringType $fixtureVo): self
    {
        $this->fixtureVo = $fixtureVo;
        return $this;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }
}
