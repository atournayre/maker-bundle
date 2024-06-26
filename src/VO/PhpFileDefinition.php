<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO;

use Aimeos\Map;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\Constant;
use Nette\PhpGenerator\EnumCase;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\TraitUse;
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

final class PhpFileDefinition
{
    private bool $strictTypes = true;

    /** @var string[] */
    private array $comments = [];

    /** @var string[] */
    private array $classComments = [];

    /** @var array<string, null|string> */
    private array $uses = [];

    /** @var string[] */
    private array $usesFunctions = [];

    /** @var Attribute[] */
    private array $attributes = [];

    private bool $interface = false;

    private bool $trait = false;

    private bool $enum = false;

    private bool $readonly = false;

    private bool $final = true;

    private bool $abstract = false;

    private ?string $extends = null;

    /** @var string[] */
    private array $implements = [];

    /** @var Constant[] */
    private array $constants = [];

    /** @var string[]|TraitUse[] */
    private array $traits = [];

    /** @var Property[] */
    private array $properties = [];

    /** @var Method[] */
    private array $methods = [];

    /** @var EnumCase[] */
    private array $enumCases = [];

    private ?string $enumType = null;

    private function __construct(
        public readonly string $namespace,
        public string $className,
    )
    {
    }

    public static function create(string $namespace, string $className): self
    {
        $className = u($className)->title()->toString();
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

    /**
     * @return string[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @param string[] $comments
     * @return $this
     */
    public function setComments(array $comments): self
    {
        Assert::allString($comments, 'Comments must be an array of strings');
        $this->comments = $comments;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getClassComments(): array
    {
        return $this->classComments;
    }

    /**
     * @param string[] $classComments
     * @return $this
     */
    public function setClassComments(array $classComments): self
    {
        Assert::allString($classComments, 'Class comments must be an array of strings');
        $this->classComments = $classComments;
        return $this;
    }

    /**
     * @return array<string, null|string>
     */
    public function getUses(): array
    {
        return $this->uses;
    }

    /**
     * @param array<int|string, null|string> $uses
     * @return $this
     */
    public function setUses(array $uses): self
    {
        $usesWithAlias = [];
        foreach ($uses as $use => $alias) {
            if (is_int($use)) {
                $use = $alias;
                $alias = null;
            }

            $usesWithAlias[$use] = $alias;
        }

        ksort($usesWithAlias);

        $this->uses = $usesWithAlias;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getUsesFunctions(): array
    {
        return $this->usesFunctions;
    }

    /**
     * @param string[] $usesFunctions
     * @return $this
     */
    public function setUsesFunctions(array $usesFunctions): self
    {
        $this->usesFunctions = $usesFunctions;
        return $this;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param Attribute[] $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): self
    {
        Assert::allIsInstanceOf($attributes, Attribute::class, 'Attributes must be an array of Attribute');

        usort($attributes, static fn(Attribute $a, Attribute $b): int => $a->getName() <=> $b->getName());

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

    public function isEnum(): bool
    {
        return $this->enum;
    }

    public function setEnum(bool $enum = true): self
    {
        $this->enum = $enum;
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

        $this->extends = $extends;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getImplements(): array
    {
        return $this->implements;
    }

    /**
     * @param string[] $implements
     * @return $this
     */
    public function setImplements(array $implements): self
    {
        sort($implements);
        $this->implements = $implements;
        return $this;
    }

    /**
     * @return Constant[]
     */
    public function getConstants(): array
    {
        return $this->constants;
    }

    /**
     * @param Constant[] $constants
     * @return $this
     */
    public function setConstants(array $constants): self
    {
        sort($constants);
        $this->constants = $constants;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * @param string[]|TraitUse[] $traits
     * @return $this
     */
    public function setTraits(array $traits): self
    {
        $traits = array_map(
            static fn(string|TraitUse $trait): string => $trait instanceof TraitUse ? $trait->getName() : $trait,
            $traits
        );

        sort($traits);
        $this->traits = $traits;
        return $this;
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param Property[] $properties
     * @return $this
     */
    public function setProperties(array $properties): self
    {
        usort($properties, static fn(Property $a, Property $b): int => $a->getName() <=> $b->getName());

        $this->properties = $properties;
        return $this;
    }

    /**
     * @return Method[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param Method[] $methods
     * @return $this
     */
    public function setMethods(array $methods): self
    {
        $this->methods = Map::from($methods)
            ->usort(static fn(Method $a, Method $b): int => $a->getName() <=> $b->getName())
            ->rekey(static fn(Method $method): string => $method->getName())
            ->toArray();
        return $this;
    }

    public function fqcn(): string
    {
        return $this->namespace . '\\' . $this->className;
    }

    public function isClass(): bool
    {
        return !$this->interface && !$this->trait;
    }

    public function setAbstract(bool $isAbstract = true): self
    {
        $this->abstract = $isAbstract;
        return $this;
    }

    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    public function addUse(string $use, ?string $alias = null): self
    {
        $uses = $this->uses;
        $uses[$use] = $alias;
        $this->setUses($uses);

        return $this;
    }

    public function addTrait(string|TraitUse $trait): self
    {
        $trait = $trait instanceof TraitUse ? $trait->getName() : $trait;

        $traits = $this->traits;
        $traits[$trait] = $trait;
        $this->setTraits($traits);

        return $this;
    }

    public function hasMethod(string $name): bool
    {
        return Map::from($this->methods)->offsetExists($name);
    }

    public function updateMethod(string $string, Method $method): self
    {
        $methods = $this->methods;
        $methods[$string] = $method;
        $this->setMethods($methods);

        return $this;
    }

    public function addMethod(Method $method): self
    {
        $methods = $this->methods;
        $methods[$method->getName()] = $method;
        $this->setMethods($methods);

        return $this;

    }

    public function getMethod(string $string): Method
    {
        Assert::keyExists($this->methods, $string, 'Method %s not found');
        return $this->methods[$string];
    }

    public function addImplement(string $class): self
    {
        $implements = $this->implements;
        $implements[$class] = $class;
        $this->setImplements($implements);

        return $this;
    }

    public function removeUse(string $class): self
    {
        $uses = $this->uses;
        unset($uses[$class]);
        $this->setUses($uses);

        return $this;
    }

    public function renameClass(string $name): self
    {
        $this->className = $name;
        return $this;
    }

    /**
     * @return EnumCase[]
     */
    public function getEnumCases(): array
    {
        return $this->enumCases;
    }

    /**
     * @param EnumCase[] $enumCases
     */
    public function setEnumCases(array $enumCases): self
    {
        $this->enumCases = $enumCases;
        return $this;
    }

    public function getEnumType(): ?string
    {
        return $this->enumType;
    }

    public function setEnumType(?string $enumType): self
    {
        $this->enumType = $enumType;
        return $this;
    }

    public function isStringEnumType(): bool
    {
        return $this->enumType === 'string';
    }
}
