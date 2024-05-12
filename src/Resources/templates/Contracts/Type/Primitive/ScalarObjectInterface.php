<?php
declare(strict_types=1);

namespace App\Contracts\Type\Primitive;

interface ScalarObjectInterface
{
    public function isArray(): bool;

    public function isBool(): bool;

    public function isFloat(): bool;

    public function isInt(): bool;

    public function isNull(): bool;

    public function isResource(): bool;

    public function isString(): bool;

    public function toJSON(): string;
}
