<?php
declare(strict_types=1);

namespace App\Type\Primitive;

use Doctrine\Common\Collections\ArrayCollection;
use Webmozart\Assert\Assert;

abstract class AbstractCollectionType implements \ArrayAccess, \Countable
{
    private array $collection = [];

    protected function __construct(
        array $collection
    )
    {
        $this->collection = $collection;
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
            Assert::isInstanceOf($value, \get_class($firstElement));
            return;
        }

        self::assertType($value, \gettype($firstElement));
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

    private static function assertType($value, string $type, string $message = ''): void
    {
        switch ($type) {
            case 'string':
                Assert::string($value, $message);
                break;
            case 'int':
                Assert::integer($value, $message);
                break;
            case 'float':
                Assert::float($value, $message);
                break;
            case 'bool':
                Assert::boolean($value, $message);
                break;
            case 'array':
                Assert::isArray($value, $message);
                break;
            case 'object':
                Assert::object($value, $message);
                break;
            case 'null':
                Assert::null($value, $message);
                break;
            default:
                throw new \InvalidArgumentException(sprintf(
                    'Invalid type "%s". Expected one of "string", "int", "float", "bool", "array", "object" or "null".',
                    $type
                ));
        }
    }
}
