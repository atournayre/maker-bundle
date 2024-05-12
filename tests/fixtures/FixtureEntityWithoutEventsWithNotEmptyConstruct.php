<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\fixtures;

use App\Repository\FixtureEntityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FixtureEntityRepository::class)]
class FixtureEntityWithoutEventsWithNotEmptyConstruct
{
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

