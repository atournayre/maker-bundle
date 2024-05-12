<?php
declare(strict_types=1);

namespace App\Type\Primitive;

use App\Contracts\Type\Primitive\ScalarObjectInterface;
use Symfony\Component\String\UnicodeString;
use Webmozart\Assert\Assert;

class StringType extends UnicodeString implements ScalarObjectInterface
{
    public static function create(string $value): self
    {
        return new self($value);
    }

    public static function fromString(string $value): self
    {
        return self::create($value);
    }

    public function isArray(): bool
    {
        return false;
    }

    public function isBool(): bool
    {
        return false;
    }

    public function isFloat(): bool
    {
        return false;
    }

    public function isInt(): bool
    {
        return false;
    }

    public function isNull(): bool
    {
        return false;
    }

    public function isResource(): bool
    {
        return false;
    }

    public function isString(): bool
    {
        return true;
    }

    public function toJSON(): string
    {
        $str = json_encode($this->toString());
        Assert::notFalse($str, 'Failed to encode to JSON for value: ' . $this->toString());

        return $str;
    }

    public function toInt(): int
    {
        Assert::allDigits(str_split($this->toString()), 'The string must contain only digits.');

        return intval($this->toString());
    }
}
