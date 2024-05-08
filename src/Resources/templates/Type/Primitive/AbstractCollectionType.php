<?php
declare(strict_types=1);

namespace App\Type\Primitive;

use Doctrine\Common\Collections\ArrayCollection;
use Webmozart\Assert\Assert;

abstract class AbstractCollectionType implements \ArrayAccess, \Countable
{
    protected function __construct(private array $collection)
    {
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->collection);
    }

    public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    protected function offsetSetAssertion($offset, $value): void
    {
        if (is_string($offset)) {
            Assert::isMap($this->collection, 'Adding element to collection (list) using string key is not supported.');
        }
        if (is_int($offset)) {
            Assert::isList($this->collection, 'Adding element to collection (map) using integer key is not supported.');
        }

        $firstElement = reset($this->collection);

        if (\is_object($firstElement)) {
            Assert::isInstanceOf($value, $firstElement::class);
            return;
        }

        $this->assertType($value, \gettype($firstElement));
    }

    public function offsetSet($offset, $value): void
    {
        $this->offsetSetAssertion($offset, $value);
        $this->collection[$offset] = $value;
    }

    public function add($value): self
    {
        $values = $this->collection;
        $values[] = $value;

        return new static($values);
    }

    public function offsetUnset($offset): void
    {
        unset($this->collection[$offset]);
    }

    public function count(): int
    {
        return count($this->collection);
    }

    public function values(): array
    {
        return $this->collection;
    }

    public function toArrayCollection(): ArrayCollection
    {
        return new ArrayCollection($this->collection);
    }

    private function assertType($value, string $type, string $message = ''): void
    {
        match ($type) {
            'string' => Assert::string($value, $message),
            'int' => Assert::integer($value, $message),
            'float' => Assert::float($value, $message),
            'bool' => Assert::boolean($value, $message),
            'array' => Assert::isArray($value, $message),
            'object' => Assert::object($value, $message),
            'null' => Assert::null($value, $message),
            default => throw new \InvalidArgumentException(sprintf(
                'Invalid type "%s". Expected one of "string", "int", "float", "bool", "array", "object" or "null".',
                $type
            )),
        };
    }
}
