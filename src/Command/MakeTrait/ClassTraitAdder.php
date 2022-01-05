<?php

namespace Atournayre\Bundle\MakerBundle\Command\MakeTrait;

use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Util\ClassDetails;
use Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator;

class ClassTraitAdder
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
     * @param string $traitNamespace
     *
     * @return void
     */
    public function generate(string $objectNamespace, string $traitNamespace): void
    {
        $entityPath = $this->getPathOfClass($objectNamespace);

        $manipulator = $this->createClassSourceManipulator($entityPath);

        $manipulator->addTrait($traitNamespace);

        $this->saveFile($entityPath, $manipulator->getSourceCode());
    }

    /**
     * @param string $class
     *
     * @return string
     */
    private function getPathOfClass(string $class): string
    {
        $classDetails = new ClassDetails($class);

        return $classDetails->getPath();
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
