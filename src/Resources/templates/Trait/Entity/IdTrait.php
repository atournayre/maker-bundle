<?php
declare(strict_types=1);

namespace App\Trait\Entity;

use Doctrine\ORM\Mapping as ORM;

trait IdTrait
{
	use IsTrait;

	#[ORM\Id]
	#[ORM\Column]
	#[ORM\GeneratedValue]
	private int $id;


	function getId(): int
	{
		return $this->id;
	}
}
