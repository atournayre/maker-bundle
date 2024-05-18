<?php

/**
 * This file has been auto-generated
 */

declare(strict_types=1);

namespace App\DTO;

use App\Contracts\Null\NullableInterface;
use App\Trait\NotNullableTrait;
use App\Type\Primitive\StringType;
use DateTimeInterface;

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

    /** Nullable because this object has no default value. */
    public ?DateTimeInterface $date = null;

    /** Nullable because this object has no default value. */
    public ?StringType $fixtureVo = null;
    public int $id = 0;
    public ?string $name = null;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'];
        $dto->name = $data['name'];
        $dto->date = $data['date'];
        $dto->fixtureVo = $data['fixtureVo'];

        return $dto;
    }

    public function validate(): array
    {
        $errors = [];

        if ('' == $this->id) {
            $errors['id'] = 'validation.dummy.id.empty';
        }
        if ('' == $this->name) {
            $errors['name'] = 'validation.dummy.name.empty';
        }
        if ('' == $this->date) {
            $errors['date'] = 'validation.dummy.date.empty';
        }
        if ('' == $this->fixtureVo) {
            $errors['fixtureVo'] = 'validation.dummy.fixtureVo.empty';
        }

        // Add more validation rules here

        return $errors;
    }
}
