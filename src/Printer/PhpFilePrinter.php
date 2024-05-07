<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Printer;

use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

final class PhpFilePrinter
{
    private function __construct(
        private readonly PhpFile $phpFile,
    )
    {
    }

    public static function create(PhpFileDefinition $phpFileDefinition): self
    {
        $phpFile = new PhpFile();
        $phpFile->setStrictTypes($phpFileDefinition->isStrictTypes());

        $fqcn = $phpFileDefinition->fqcn();
        if ($phpFileDefinition->isInterface()) {
            $phpFile->addInterface($fqcn);
        } elseif ($phpFileDefinition->isTrait()) {
            $phpFile->addTrait($fqcn);
        } else {
            $phpFile->addClass($fqcn);
        }

        $classes = $phpFile->getClasses();
        $classNameIdentifier = array_key_first($classes);
        Assert::keyExists($classes, $classNameIdentifier, 'No class found in PhpFile');

        Assert::allNullOrIsInstanceOf($phpFileDefinition->getProperties(), Property::class);

        $class = $classes[$classNameIdentifier];

        if (null !== $phpFileDefinition->getExtends()) {
            $class->setExtends($phpFileDefinition->getExtends());
        }

        $class
            ->setConstants($phpFileDefinition->getConstants())
            ->setMethods($phpFileDefinition->getMethods())
        ;

        if ($class->isClass()) {
            $class
                ->setFinal($phpFileDefinition->isFinal())
                ->setReadOnly($phpFileDefinition->isReadonly())
                ->setProperties($phpFileDefinition->getProperties())
            ;
        }

        $namespace = $class->getNamespace();

        foreach ($phpFileDefinition->getTraits() as $trait) {
            $class->removeTrait($trait);
            $class->addTrait($trait);
            $namespace->addUse($trait);
        }

        foreach ($phpFileDefinition->getComments() as $comment) {
            $phpFile->addComment($comment);
        }

        foreach ($phpFileDefinition->getUses() as $use) {
            $namespace->addUse($use);
        }

        /** @var Attribute $attribute */
        foreach ($phpFileDefinition->getAttributes() as $attribute) {
            $namespace->addUse($attribute->getName());
            $class->addAttribute($attribute->getName(), $attribute->getArguments());
        }

        foreach ($phpFileDefinition->getImplements() as $implement) {
            $namespace->addUse($implement);
            $class->addImplement($implement);
        }

        foreach ($phpFileDefinition->getProperties() as $property) {
            if (str_contains($property->getType(), '\\')) {
                $namespace->addUse($property->getType());
            }
        }

        return new self($phpFile);
    }

    public function print(): string
    {
        return (new Printer())
            ->printFile($this->phpFile);
    }
}
