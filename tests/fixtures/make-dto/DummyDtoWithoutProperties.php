<?php

/**
 * This file has been auto-generated
 */

declare(strict_types=1);

namespace App\DTO;

use App\Contracts\Null\NullableInterface;
use App\Trait\NotNullableTrait;

final class Dummy implements NullableInterface
{
    use NotNullableTrait;

    public static function fromArray(array $data): self
    {
        $dto = new self();

        return $dto;
    }

    public function validate(): array
    {
        $errors = [];



        // Add more validation rules here

        return $errors;
    }
}
