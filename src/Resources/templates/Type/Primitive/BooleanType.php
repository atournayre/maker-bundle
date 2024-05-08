<?php
declare(strict_types=1);

namespace App\Type\Primitive;

use App\Contracts\Type\Primitive\ScalarObjectInterface;
use Webmozart\Assert\Assert;

class BooleanType implements ScalarObjectInterface
{
    private function __construct(private readonly bool $value)
    {
    }

    public static function create(bool $value): self
    {
        return new self($value);
    }

    public function isArray(): bool
    {
        return false;
    }

    public function isBool(): bool
    {
        return true;
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
        return false;
    }

    public function toJSON(): string
    {
        $str = json_encode($this->toString());
        Assert::notFalse($str, 'Failed to encode to JSON for value: ' . $this->toString());

        return $str;
    }

    public function toString(): string
    {
        return $this->value ? 'true' : 'false';
    }

    public function toInt(): int
    {
        return intval($this->value);
    }

    public function asInteger(): IntegerType
    {
        return IntegerType::create($this->toInt());
    }

    public function isTrue(): bool
    {
        return $this->value;
    }

    public function isFalse(): bool
    {
        return !$this->value;
    }
}
