<?php

/**
 * This file has been auto-generated
 */

declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\fixtures;

use App\Collection\EventCollection;
use App\Contracts\Event\HasEventsInterface;
use App\Repository\FixtureEntityRepository;
use App\Trait\EventsTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass: FixtureEntityRepository::class)]
class Dummy implements HasEventsInterface
{
    use EventsTrait;

    public ?int $fixed = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    public function __construct()
    {
        $this->fixed = 1;
        $this->events = EventCollection::createAsList([]);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
