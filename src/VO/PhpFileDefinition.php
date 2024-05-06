<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO;

use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\Constant;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

final class PhpFileDefinition
{
    private bool $strictTypes = true;
    private array $comments = [];
    private array $uses = [];
    private array $attributes = [];
    private bool $interface = false;
    private bool $trait = false;
    private bool $readonly = false;
    private bool $final = true;
    private ?string $extends = null;
    private array $implements = [];
    private array $constants = [];
    private array $traits = [];
    private array $properties = [];
    private array $methods = [];

    private function __construct(
        public readonly string $namespace,
        public readonly string $className,
    )
    {
    }

    public static function create(string $namespace, string $className): self
    {
        return new self($namespace, $className);
    }

    public function isStrictTypes(): bool
    {
        return $this->strictTypes;
    }

    public function setStrictTypes(bool $strictTypes): void
    {
        $this->strictTypes = $strictTypes;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function setComments(array $comments): void
    {
        Assert::allString($comments, 'Comments must be an array of strings');
        $this->comments = $comments;
    }

    public function getUses(): array
    {
        return $this->uses;
    }

    public function setUses(array $uses): void
    {
        $this->uses = $uses;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        Assert::allIsInstanceOf($attributes, Attribute::class, 'Attributes must be an array of Attribute');
        $this->attributes = $attributes;
    }

    public function isInterface(): bool
    {
        return $this->interface;
    }

    public function setInterface(bool $interface): void
    {
        $this->interface = $interface;
    }

    public function isTrait(): bool
    {
        return $this->trait;
    }

    public function setTrait(bool $trait): void
    {
        $this->trait = $trait;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    public function setReadonly(bool $readonly): void
    {
        $this->readonly = $readonly;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }

    public function setFinal(bool $final): void
    {
        $this->final = $final;
    }

    public function getExtends(): ?string
    {
        return $this->extends;
    }

    public function setExtends(?string $extends): void
    {
        if (null === $extends) {
            return;
        }

        Assert::classExists($extends, 'Class %s does not exist');
        $this->extends = $extends;
    }

    public function getImplements(): array
    {
        return $this->implements;
    }

    public function setImplements(array $implements): void
    {
        Assert::allClassExists($implements, 'Class %s does not exist');
        $this->implements = $implements;
    }

    public function getConstants(): array
    {
        return $this->constants;
    }

    public function setConstants(array $constants): void
    {
        Assert::allIsInstanceOf($constants, Constant::class, 'Constants must be an array of Constant');
        $this->constants = $constants;
    }

    public function getTraits(): array
    {
        return $this->traits;
    }

    public function setTraits(array $traits): void
    {
        Assert::allString($traits, 'Traits must be an array of strings');
        Assert::allClassExists($traits, 'Trait %s does not exist');
        $this->traits = $traits;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): void
    {
        Assert::allIsInstanceOf($properties, Property::class, 'Properties must be an array of Property');
        $this->properties = $properties;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function setMethods(array $methods): void
    {
        Assert::allIsInstanceOf($methods, Method::class, 'Methods must be an array of Method');
        $this->methods = $methods;
    }

    public function fqcn(): string
    {
        return $this->namespace . '\\' . $this->className;
    }
}
