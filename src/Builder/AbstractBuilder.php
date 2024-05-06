<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
use Atournayre\Bundle\MakerBundle\Contracts\PhpFileBuilderInterface;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\Printer\Printer;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

abstract class AbstractBuilder implements PhpFileBuilderInterface
{
    protected PhpFileDefinition $phpFileDefinition;
    private bool $created = false;

    abstract public function supports(string $makerConfigurationClassName): bool;

    public function create(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition = PhpFileDefinition::create(
            $makerConfiguration->namespace(),
            $makerConfiguration->classname()
        );
        $this->setStrictTypes($makerConfiguration);
        $this->setComments($makerConfiguration);
        $this->addUses($makerConfiguration);
        $this->addAttributes($makerConfiguration);
        $this->setExtends($makerConfiguration);
        $this->addImplements($makerConfiguration);
        $this->addConstants($makerConfiguration);
        $this->addTraits($makerConfiguration);
        $this->addProperties($makerConfiguration);
        $this->addMethods($makerConfiguration);
        $this->created = true;
    }

    public function createInstance(MakerConfigurationInterface $makerConfiguration): PhpFileDefinition
    {
        $this->created = true;

        return PhpFileDefinition::create(
            $makerConfiguration->namespace(),
            $makerConfiguration->classname()
        );
    }

    public function setStrictTypes(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setStrictTypes(true);
    }

    public function setComments(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setComments([
            'This file has been auto-generated',
        ]);
    }

    public function addUses(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setUses([]);
    }

    public function addAttributes(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setAttributes([]);
    }

    public function setInterface(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setInterface(false);
    }

    public function setTrait(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setTrait(false);
    }

    public function setReadonly(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setReadonly(true);
    }

    public function setFinal(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setFinal(true);
    }

    public function setExtends(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setExtends(null);
    }

    public function addImplements(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setImplements([]);
    }

    public function addConstants(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setConstants([]);
    }

    public function addTraits(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setTraits([]);
    }

    public function addProperties(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setProperties([]);
    }

    public function addMethods(MakerConfigurationInterface $makerConfiguration): void
    {
        $this->phpFileDefinition->setMethods([]);
    }

    /**
     * @throws \Exception
     */
    public function setNamespace(MakerConfigurationInterface $makerConfiguration): void
    {
        throw new \Exception('Use constructor to set namespace');
    }

    /**
     * @throws \Exception
     */
    public function setClassName(MakerConfigurationInterface $makerConfiguration): void
    {
        throw new \Exception('Use constructor to set class name');
    }

    public function phpFile(): PhpFile
    {
        $phpFile = new PhpFile();
        $phpFile->setStrictTypes($this->phpFileDefinition->isStrictTypes());

        $fqcn = $this->phpFileDefinition->fqcn();
        if ($this->phpFileDefinition->isInterface()) {
            $phpFile->addInterface($fqcn);
        } elseif ($this->phpFileDefinition->isTrait()) {
            $phpFile->addTrait($fqcn);
        } else {
            $phpFile->addClass($fqcn);
        }

        $classes = $phpFile->getClasses();
        $classNameIdentifier = array_key_first($classes);
        Assert::keyExists($classes, $classNameIdentifier, 'No class found in PhpFile');

        Assert::allNullOrIsInstanceOf($this->phpFileDefinition->getProperties(), Property::class);

        $class = $classes[$classNameIdentifier];
        $class
            ->setFinal($this->phpFileDefinition->isFinal())
            ->setReadOnly($this->phpFileDefinition->isReadonly())
            ->setExtends($this->phpFileDefinition->getExtends())
            ->setConstants($this->phpFileDefinition->getConstants())
            ->setTraits($this->phpFileDefinition->getTraits())
            ->setProperties($this->phpFileDefinition->getProperties())
            ->setMethods($this->phpFileDefinition->getMethods())
        ;

        foreach ($this->phpFileDefinition->getComments() as $comment) {
            $phpFile->addComment($comment);
        }

        foreach ($this->phpFileDefinition->getUses() as $use) {
            $phpFile->addUse($use);
        }

        foreach ($this->phpFileDefinition->getAttributes() as $attribute) {
            $phpFile->addUse($attribute);
            $class->addAttribute($attribute);
        }

        foreach ($this->phpFileDefinition->getImplements() as $implement) {
            $phpFile->addUse($implement);
            $class->addImplement($implement);
        }

        return $phpFile;
    }

    public function printPhpFile(?MakerConfigurationInterface $makerConfiguration = null): string
    {
        if (null === $makerConfiguration) {
            Assert::true($this->created, 'You must create the file before printing it or provide a MakerConfiguration to print it directly.');
        }

        if (!$this->created) {
            $this->create($makerConfiguration);
        }
        $phpFile = $this->phpFile();

        return (new Printer())
            ->printFile($phpFile);
    }

    /**
     * @param MakerConfigurationInterface $makerConfiguration
     * @return array<string, string>
     */
    protected function correspondingTypes(MakerConfigurationInterface $makerConfiguration): array
    {
        $rootDir = $makerConfiguration->rootDir();
        $allowedTypes = $makerConfiguration->propertiesAllowedTypes();

        $allowedTypesMapping = [];
        foreach ($allowedTypes as $allowedType) {
            if (!str_contains($allowedType, '/')) {
                $allowedTypesMapping[$allowedType] = $allowedType;
                continue;
            }
            $namespaceFromPath = Str::namespaceFromPath($allowedType, $rootDir);
            $rootNamespace = $makerConfiguration->rootNamespace();
            $namespace = Str::prefixByRootNamespace($namespaceFromPath, $rootNamespace);
            $allowedTypesMapping[$allowedType] = $namespace;
        }
        return $allowedTypesMapping;
    }
}
