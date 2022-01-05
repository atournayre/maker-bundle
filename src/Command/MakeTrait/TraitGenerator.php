<?php

namespace Atournayre\Bundle\MakerBundle\Command\MakeTrait;

use Composer\Autoload\ClassLoader;
use Exception;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator;

class TraitGenerator
{
    protected Generator   $generator;
    protected FileManager $fileManager;

    /**
     * @param Generator   $generator
     * @param FileManager $fileManager
     */
    public function __construct(Generator $generator, FileManager $fileManager)
    {
        $this->generator = $generator;
        $this->fileManager = $fileManager;
    }

    /**
     * @param string $objectNamespace
     * @param bool   $regenerate
     *
     * @return ClassNameDetails
     * @throws Exception
     */
    public function generate(string $objectNamespace, bool $regenerate = false): ClassNameDetails
    {
        $name = Str::getShortClassName($objectNamespace);

        $classNameDetails = $this->generator
            ->createClassNameDetails($this->setName($name), 'Traits\\');

        if ($regenerate) {
            $this->removeTraitFile($classNameDetails->getFullName());
        }

        $entityPath = $this->generator->generateClass($classNameDetails->getFullName(), 'Class.tpl.php');
        $this->generator->writeChanges();

        $manipulator = $this->createClassSourceManipulator($entityPath);

        $manipulator->addUseStatementIfNecessary($objectNamespace);

        $property = Str::asLowerCamelCase($name);
        $type = Str::asCamelCase($property);

        $manipulator->addProperty($property);
        $this->addGetter($manipulator, $property, $type);
        $this->addSetter($manipulator, $property, $type, $name);

        $sourceCode = $this->adjustSourceCode($manipulator, $name, $property, $type);
        $this->saveFile($entityPath, $sourceCode);

        return $classNameDetails;
    }

    /**
     * @param string $traitFullName
     *
     * @return void
     */
    protected function removeTraitFile(string $traitFullName): void
    {
        /** @var ClassLoader $classLoader */
        $classLoader = current(ClassLoader::getRegisteredLoaders());
        $file = $classLoader->findFile($traitFullName);
        if(!$file || !$this->fileManager->fileExists($file)) {
            return;
        }
        unlink(realpath($file));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function setName(string $name): string
    {
        return sprintf('%sTrait', $name);
    }

    /**
     * @param ClassSourceManipulator $manipulator
     * @param string                 $property
     * @param string                 $type
     *
     * @return void
     */
    protected function addGetter(ClassSourceManipulator $manipulator, string $property, string $type): void
    {
        $manipulator
            ->addGetter($property, $type, false, [
                sprintf('@return %s', $type),
            ]);
    }

    /**
     * @param ClassSourceManipulator $manipulator
     * @param string                 $property
     * @param string                 $type
     * @param string                 $name
     *
     * @return void
     */
    protected function addSetter(ClassSourceManipulator $manipulator, string $property, string $type, string $name): void
    {
        $manipulator
            ->addSetter($property, $type, false, [
                sprintf('@param %s $%s', $type, $property),
                '',
                sprintf('@return %s|%s', $this->setName($name), $type),
            ]);
    }

    /**
     * @param string                 $className
     * @param ClassSourceManipulator $manipulator
     *
     * @return string
     */
    protected function transformClassIntoTrait(string $className, ClassSourceManipulator $manipulator): string
    {
        return str_replace(
            sprintf('class %s', $className),
            sprintf('trait %s', $className),
            $manipulator->getSourceCode()
        );
    }

    /**
     * @param ClassSourceManipulator $manipulator
     * @param string                 $className
     * @param string                 $property
     * @param string                 $type
     *
     * @return string
     */
    protected function adjustSourceCode(
        ClassSourceManipulator $manipulator,
        string $className,
        string $property,
        string $type
    ): string
    {
        $sourceCode = str_replace(
            sprintf('class %s', $className),
            sprintf('trait %s', $className),
            $manipulator->getSourceCode()
        );
        return str_replace(
            sprintf('private $%s', $property),
            sprintf('private %s $%s', $type, $property),
            $sourceCode
        );
    }

    /**
     * @param string $entityPath
     * @param string $sourceCode
     *
     * @return void
     */
    protected function saveFile(string $entityPath, string $sourceCode): void
    {
        $this->fileManager->dumpFile($entityPath, $sourceCode);
    }

    /**
     * @param string $entityPath
     *
     * @return ClassSourceManipulator
     */
    protected function createClassSourceManipulator(string $entityPath): ClassSourceManipulator
    {
        return new ClassSourceManipulator(
            $this->fileManager->getFileContents($entityPath),
            true,
            false,
            true,
            false
        );
    }
}
