<?php
declare(strict_types=1);

namespace App\Trait;

use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;

trait IdEntityTrait
{
	use EntityIsTrait;

	#[ORM\Id]
	#[ORM\Column]
	#[ORM\GeneratedValue]
	private int $id;


	function getId(): int
	{
		return $this->id;
	}
}
