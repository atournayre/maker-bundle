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

    public function setStrictTypes(bool $strictTypes): self
    {
        $this->strictTypes = $strictTypes;
        return $this;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function setComments(array $comments): self
    {
        Assert::allString($comments, 'Comments must be an array of strings');
        $this->comments = $comments;
        return $this;
    }

    public function getUses(): array
    {
        return $this->uses;
    }

    public function setUses(array $uses): self
    {
        $this->uses = $uses;
        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): self
    {
        Assert::allIsInstanceOf($attributes, Attribute::class, 'Attributes must be an array of Attribute');
        $this->attributes = $attributes;
        return $this;
    }

    public function isInterface(): bool
    {
        return $this->interface;
    }

    public function setInterface(bool $interface = true): self
    {
        $this->interface = $interface;
        return $this;
    }

    public function isTrait(): bool
    {
        return $this->trait;
    }

    public function setTrait(bool $trait = true): self
    {
        $this->trait = $trait;
        return $this;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    public function setReadonly(bool $readonly = true): self
    {
        $this->readonly = $readonly;
        return $this;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }

    public function setFinal(bool $final = true): self
    {
        $this->final = $final;
        return $this;
    }

    public function getExtends(): ?string
    {
        return $this->extends;
    }

    public function setExtends(?string $extends): self
    {
        if (null === $extends) {
            return $this;
        }

        Assert::classExists($extends, 'Class %s does not exist');
        $this->extends = $extends;
        return $this;
    }

    public function getImplements(): array
    {
        return $this->implements;
    }

    public function setImplements(array $implements): self
    {
        Assert::allClassExists($implements, 'Class %s does not exist');
        $this->implements = $implements;
        return $this;
    }

    public function getConstants(): array
    {
        return $this->constants;
    }

    public function setConstants(array $constants): self
    {
        Assert::allIsInstanceOf($constants, Constant::class, 'Constants must be an array of Constant');
        $this->constants = $constants;
        return $this;
    }

    public function getTraits(): array
    {
        return $this->traits;
    }

    public function setTraits(array $traits): self
    {
        Assert::allString($traits, 'Traits must be an array of strings');
        Assert::allClassExists($traits, 'Trait %s does not exist');
        $this->traits = $traits;
        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): self
    {
        Assert::allIsInstanceOf($properties, Property::class, 'Properties must be an array of Property');
        $this->properties = $properties;
        return $this;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function setMethods(array $methods): self
    {
        Assert::allIsInstanceOf($methods, Method::class, 'Methods must be an array of Method');
        $this->methods = $methods;
        return $this;
    }

    public function fqcn(): string
    {
        return $this->namespace . '\\' . $this->className;
    }
}
