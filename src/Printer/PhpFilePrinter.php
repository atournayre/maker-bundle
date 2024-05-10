<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Printer;

use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\EnumType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\TraitType;
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
        $phpFile = match (true) {
            $phpFileDefinition->isInterface() => self::createInterface($phpFileDefinition),
            $phpFileDefinition->isEnum() => self::createEnum($phpFileDefinition),
            $phpFileDefinition->isTrait() => self::createTrait($phpFileDefinition),
            default => self::createClass($phpFileDefinition),
        };

        return new self($phpFile);
    }

    public function print(): string
    {
        return (new Printer())
            ->printFile($this->phpFile);
    }

    private static function createClass(PhpFileDefinition $phpFileDefinition): PhpFile
    {
        $phpFile = new PhpFile();

        $fqcn = $phpFileDefinition->fqcn();
        $phpFile->addClass($fqcn);

        Assert::allNullOrIsInstanceOf($phpFileDefinition->getProperties(), Property::class);

        $class = self::getClass($phpFile);

        if (null !== $phpFileDefinition->getExtends()) {
            $class->setExtends($phpFileDefinition->getExtends());
        }

        $class
            ->setConstants($phpFileDefinition->getConstants())
            ->setMethods($phpFileDefinition->getMethods())
            ->setFinal($phpFileDefinition->isFinal())
            ->setReadOnly($phpFileDefinition->isReadonly())
            ->setAbstract($phpFileDefinition->isAbstract())
            ->setProperties($phpFileDefinition->getProperties())
        ;

        return self::commonParts($phpFile, $phpFileDefinition);
    }

    private static function createInterface(PhpFileDefinition $phpFileDefinition): PhpFile
    {
        $phpFile = new PhpFile();

        $fqcn = $phpFileDefinition->fqcn();
        $phpFile->addInterface($fqcn);

        $class = self::getClass($phpFile);

        if (null !== $phpFileDefinition->getExtends()) {
            $class->setExtends($phpFileDefinition->getExtends());
        }

        $class
            ->setConstants($phpFileDefinition->getConstants())
            ->setMethods($phpFileDefinition->getMethods())
        ;

        return self::commonParts($phpFile, $phpFileDefinition);
    }

    private static function createTrait(PhpFileDefinition $phpFileDefinition): PhpFile
    {
        $phpFile = new PhpFile();

        $fqcn = $phpFileDefinition->fqcn();
        $phpFile->addTrait($fqcn);

        Assert::allNullOrIsInstanceOf($phpFileDefinition->getProperties(), Property::class);

        $class = self::getClass($phpFile);

        $class
            ->setConstants($phpFileDefinition->getConstants())
            ->setMethods($phpFileDefinition->getMethods())
            ->setProperties($phpFileDefinition->getProperties())
        ;

        return self::commonParts($phpFile, $phpFileDefinition);
    }

    private static function createEnum(PhpFileDefinition $phpFileDefinition): PhpFile
    {
        $phpFile = new PhpFile();

        $fqcn = $phpFileDefinition->fqcn();
        $enum = $phpFile->addEnum($fqcn);
        $enum->setType($phpFileDefinition->getEnumType());

        foreach ($phpFileDefinition->getEnumCases() as $enumType) {
            $enumTypeValue = $phpFileDefinition->isStringEnumType() ? $enumType->getValue() : (int)$enumType->getValue();
            $enum->addCase($enumType->getName(), $enumTypeValue);
        }

        $class = self::getClass($phpFile);

        $class
            ->setConstants($phpFileDefinition->getConstants())
            ->setMethods($phpFileDefinition->getMethods())
        ;

        return self::commonParts($phpFile, $phpFileDefinition);
    }

    private static function getClass(PhpFile $phpFile): ClassType|TraitType|InterfaceType|EnumType
    {
        $classes = $phpFile->getClasses();
        $classNameIdentifier = array_key_first($classes);
        Assert::keyExists($classes, $classNameIdentifier, 'No class found in PhpFile');

        return $classes[$classNameIdentifier];
    }

    private static function commonParts(PhpFile $phpFile, PhpFileDefinition $phpFileDefinition): PhpFile
    {
        $phpFile->setStrictTypes($phpFileDefinition->isStrictTypes());

        foreach ($phpFileDefinition->getComments() as $comment) {
            $phpFile->addComment($comment);
        }

        $classes = $phpFile->getClasses();
        $classNameIdentifier = array_key_first($classes);
        Assert::keyExists($classes, $classNameIdentifier, 'No class found in PhpFile');

        Assert::allNullOrIsInstanceOf($phpFileDefinition->getProperties(), Property::class);

        $class = $classes[$classNameIdentifier];

        $namespace = $class->getNamespace();

        foreach ($phpFileDefinition->getTraits() as $trait) {
            $class->addTrait($trait);
            $namespace->addUse($trait);
        }

        foreach ($phpFileDefinition->getClassComments() as $comment) {
            $class->addComment($comment);
        }

        foreach ($phpFileDefinition->getUses() as $use => $alias) {
            $namespace->addUse($use, $alias);
        }

        foreach ($phpFileDefinition->getUsesFunctions() as $use => $alias) {
            $namespace->addUseFunction($use, $alias);
        }

        foreach ($phpFileDefinition->getAttributes() as $attribute) {
            $namespace->addUse($attribute->getName());
            $class->addAttribute($attribute->getName(), $attribute->getArguments());
        }

        foreach ($phpFileDefinition->getImplements() as $implement) {
            $namespace->addUse($implement);
            $class->addImplement($implement);
        }

        foreach ($phpFileDefinition->getProperties() as $property) {
            if (str_contains((string)$property->getType(), '\\')) {
                $namespace->addUse($property->getType());
            }
        }

        return $phpFile;
    }
}
