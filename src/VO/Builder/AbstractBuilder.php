<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\EnumType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\TraitType;
use Webmozart\Assert\Assert;

abstract class AbstractBuilder
{
    protected PhpFile $file;

    protected function __construct(
        protected FileDefinition $fileDefinition,
    )
    {
    }

    abstract public static function build(FileDefinition $fileDefinition): self;

    public function generate(): string
    {
        return (string)$this->file;
    }

    protected function withFile(PhpFile $file): self
    {
        $instance = clone $this;
        $instance->file = $file;

        return $instance;
    }

    protected function withUses(array $uses): self
    {
        $clone = clone $this;

        foreach ($uses as $classname => $alias) {
            $clone = $clone->withUse($classname, $alias);
        }

        return $clone;
    }

    protected function withUse(?string $classname, ?string $alias = null): self
    {
        if (null === $classname) {
            return $this;
        }

        $clone = clone $this;
        $class = $clone->getClass();

        $namespace = $class->getNamespace();
        $namespace->addUse($classname, $alias);

        return $clone;
    }

    public function getClass(): ClassType|TraitType|InterfaceType|EnumType
    {
        return $this->file->getClasses()[$this->fileDefinition->fullName()];
    }

    protected function setAttributes(array $attributes): self
    {
        Assert::allIsInstanceOf($attributes, Attribute::class);

        $clone = clone $this;
        $class = $clone->getClass();
        $class->setAttributes($attributes);

        foreach ($attributes as $attribute) {
            $attributeName = '\\' . $attribute->getName();
            $class->getNamespace()->addUse($attributeName);
        }

        return $clone;
    }

    protected function createFile(): self
    {
        $file = new PhpFile;
        $file->addComment('This file has been auto-generated');
        $file->setStrictTypes();
        $file->addClass($this->fileDefinition->fullName())
            ->setFinal();

        $clone = clone $this;
        $clone->file = $file;

        return $clone;
    }

    protected function addComment(string|array $comment): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $comments = is_array($comment) ? $comment : [$comment];

        foreach ($comments as $comment) {
            $class->addComment($comment);
        }

        return $clone;
    }

    protected function extends(string $extends): self
    {
        $clone = clone $this;
        $class = $clone->getClass();
        $class->setExtends($extends);

        return $clone;
    }

    protected function withProperties(array $properties): self
    {
        Assert::allIsInstanceOf($properties, Property::class);

        $clone = clone $this;
        $class = $clone->getClass();

        foreach ($properties as $property) {
            $class->addMember($property);
        }

        return $clone;
    }

    protected function addMember($member = null): self
    {
        if (null === $member) {
            return $this;
        }

        $clone = clone $this;
        $class = $clone->getClass();
        $class->addMember($member);

        return $clone;
    }

    protected function createFileAsInterface(): self
    {
        $file = new PhpFile;
        $file->addComment('This file has been auto-generated');
        $file->setStrictTypes();
        $file->addInterface($this->fileDefinition->fullName());

        $clone = clone $this;
        $clone->file = $file;

        return $clone;
    }

    protected function createFileAsTrait(): self
    {
        $file = new PhpFile;
        $file->addComment('This file has been auto-generated');
        $file->setStrictTypes();
        $file->addTrait($this->fileDefinition->fullName());

        $clone = clone $this;
        $clone->file = $file;

        return $clone;
    }

    protected function addImplement(string $interface): self
    {
        $clone = clone $this;
        $class = $clone->getClass();
        $class->addImplement($interface);

        return $clone;
    }

    protected function addMembers(array $members): self
    {
        Assert::allIsInstanceOfAny($members, [
            Property::class,
            Method::class,
        ]);

        $clone = clone $this;
        $class = $clone->getClass();

        foreach ($members as $member) {
            $class->addMember($member);
        }

        return $clone;
    }

    protected function isReadOnly(bool $state = true): self
    {
        $clone = clone $this;
        $class = $clone->getClass();
        $class->setReadOnly($state);

        return $clone;
    }

    protected function changeClassName(string $className): self
    {
        $clone = clone $this;

        $namespace = $clone->file->getNamespaces()[$this->fileDefinition->namespace()];

        $classes = $namespace->getClasses();
        $classesKeys = array_keys($classes);
        $identifierForTemplateClass = current($classesKeys);

        /** @var ClassType $newClass */
        $newClass = clone $classes[$identifierForTemplateClass];
        $newClass->setName(Str::classNameFromNamespace($className, ''));

        $namespace->add($newClass);
        $namespace->removeClass($identifierForTemplateClass);

        return $clone;
    }

    protected function createFromCode(string $code): self
    {
        $file = PhpFile::fromCode($code);

        $clone = clone $this;
        $clone->file = $file;

        return $clone;
    }

    protected function removeUse(string $use): self
    {
        $clone = clone $this;
        $class = $clone->getClass();
        $class->getNamespace()->removeUse($use);

        return $clone;
    }

    protected function addTrait(string $trait): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $class->removeTrait($trait);
        $class->addTrait($trait);

        return $clone
            ->withUse($trait);
    }

    protected function updateMethod(string $methodName, Method $method): self
    {
        $clone = clone $this;
        $class = $clone->getClass();
        $class->removeMethod($methodName);
        $class->addMember($method);

        return $clone;
    }
}
