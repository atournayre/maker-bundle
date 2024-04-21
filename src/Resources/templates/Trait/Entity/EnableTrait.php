<?php
declare(strict_types=1);

namespace App\Trait\Entity;

use Doctrine\ORM\Mapping as ORM;

trait EnableTrait
{
    #[ORM\Column]
    private ?bool $enable = false;

    public function isEnabled(): ?bool
    {
        return $this->enable;
    }

    public function setEnable(bool $enable): self
    {
        $this->enable = $enable;

        return $this;
    }

    public function enable(): void
    {
        $this->enable = true;
    }

    public function disable(): void
    {
        $this->enable = false;
    }
}
