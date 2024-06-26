<?php
declare(strict_types=1);

namespace App\Trait\Entity;

use App\Contracts\Security\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait UpdatedByTrait
{
    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(name: 'updated_by', referencedColumnName: 'id')]
    #[Gedmo\Blameable(on: 'update')]
    private UserInterface $updatedBy;

    public function getUpdatedBy(): UserInterface
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(UserInterface $user): self
    {
        $this->updatedBy = $user;
        return $this;
    }
}
