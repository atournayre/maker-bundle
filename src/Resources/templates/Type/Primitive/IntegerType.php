<?php
declare(strict_types=1);

namespace App\Type\Primitive;

use App\Contracts\Type\Primitive\ScalarObjectInterface;
use Webmozart\Assert\Assert;

class IntegerType implements ScalarObjectInterface
{
    private readonly int $value;

    private function __construct(
        int $value,
        private readonly int $integerPart,
        private readonly int $decimalPart,
        private readonly int $precision
    )
    {
        Assert::integer($value, 'Value must be an integer');
        $this->value = $value;
    }

    /**
     * @param int|string|float $value
     */
    public static function create($value, ?int $precision = null): self
    {
        $precision ??= 0;

        if (is_int($value)) {
            return self::fromInt($value, $precision);
        }
        if (is_float($value)) {
            return self::fromFloat($value, $precision);
        }

        return self::fromString((string) $value, $precision);
    }

    public static function fromInt(int $value, ?int $precision = null): self
    {
        $precision ??= 0;

        if ($precision === 0) {
            $integerPart = $value;
            $decimalPart = 0;
            return new self($value, $integerPart, $decimalPart, $precision);
        }

        $integerPart = StringType::create((string) $value)->slice(0, -$precision)->toInt();
        $decimalPart = StringType::create((string) $value)->slice(-$precision)->toInt();

        return new self($value, $integerPart, $decimalPart, $precision);
    }

    public static function fromString(string $value, ?int $precision = null): self
    {
        $precision ??= 0;
        $intValue = intval($value);
        $integerPart = StringType::create($value)->slice(0, -$precision)->toInt();
        $decimalPart = StringType::create($value)->slice(-$precision)->toInt();
        return new self($intValue, $integerPart, $decimalPart, $precision);
    }

    public static function fromFloat(float $value, ?int $precision = null): self
    {
        $precision ??= 0;
        $floatAsArray = StringType::create((string) $value)->split('.');

        $integerPart = $floatAsArray[0]->toInt();
        $decimalPart = ($floatAsArray[1] ?? StringType::create('0'))
            ->padEnd($precision, '0')
            ->toInt();

        $value = StringType::create('')
            ->join([$integerPart, $decimalPart], '')
            ->toInt();

        $precision = StringType::create((string) $decimalPart)->length();

        return new self($value, $integerPart, $decimalPart, $precision);
    }

    public function precision(): int
    {
        return $this->precision;
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
        return true;
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
        $str = json_encode([
            'value' => $this->value(),
            'precision' => $this->precision,
            'asFloat' => $this->toFloat(),
        ]);
        Assert::notFalse($str, 'Failed to encode to JSON for value: ' . $this->value());

        return $str;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function even(): bool
    {
        return $this->value() % 2 === 0;
    }

    public function odd(): bool
    {
        return $this->value() % 2 === 1;
    }

    public function toFloat(): float
    {
        $floatAsString = StringType::create('')
            ->join([$this->integerPart, $this->decimalPart], '.')
            ->toString();

        return floatval($floatAsString);
    }

    public function isZero(): bool
    {
        return $this->value() === 0;
    }

    public function isPositive(): bool
    {
        return $this->value() > 0;
    }

    public function isNegative(): bool
    {
        return $this->value() < 0;
    }

    public function isGreaterThan(int $value): bool
    {
        return $this->value() > $value;
    }

    public function isGreaterThanOrEqualTo(int $value): bool
    {
        return $this->value() >= $value;
    }

    public function isLessThan(int $value): bool
    {
        return $this->value() < $value;
    }

    public function isLessThanOrEqualTo(int $value): bool
    {
        return $this->value() <= $value;
    }

    public function isBetween(int $min, int $max): bool
    {
        return $this->isGreaterThanOrEqualTo($min) && $this->isLessThanOrEqualTo($max);
    }

    public function isDivisibleBy(int $divisor): bool
    {
        return $this->value() % $divisor === 0;
    }

    public function equals(int $value): bool
    {
        return $this->value() === $value;
    }
}
