<?php

/**
 * This file has been auto-generated
 */

declare(strict_types=1);

namespace App\DTO;

use App\Contracts\Null\NullableInterface;
use App\Trait\NotNullableTrait;

/**
 * Use only for request/response data structure
 *
 * ONLY
 * - public properties
 * - primitive types : string, int, float, bool, array, null, \DateTimeInterface or DTO
 *
 * MUST NOT
 * - have getter/setter
 * - have methods except `validate`
 * - have logic in the class
 *
 * @object-type DTO
 */
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
