<?php
declare(strict_types=1);

namespace App\Manager;

use App\Collection\SplFileInfoCollection;
use App\Contracts\FileManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use function Symfony\Component\String\u;

final class FileManager implements FileManagerInterface
{
    public readonly Finder $finder;
    public readonly Filesystem $filesystem;

    private function __construct(
        public readonly string $directoryOrFile,
    ) {
        $this->finder = new Finder();
        $this->filesystem = new Filesystem();
    }

    public static function from(string $directoryOrFile): self
    {
        return new self($directoryOrFile);
    }

    public function createDirectory(string $directory): void
    {
        $directoryToCreate = $this->concatWithDirectoryOrFile($directory);

        $this->filesystem->mkdir($directoryToCreate);
    }

    private function concatWithDirectoryOrFile(string $directoryOrFile): string
    {
        return u($directoryOrFile)
            ->ensureStart('/')
            ->trimEnd('/')
            ->prepend($this->directoryOrFile)
            ->toString();
    }

    public function removeDirectory(string $directory): void
    {
        $directoryToRemove = $this->concatWithDirectoryOrFile($directory);

        $this->filesystem->remove($directoryToRemove);
    }

    public function removeFile(string $file): void
    {
        $fileToRemove = $this->concatWithDirectoryOrFile($file);

        $this->filesystem->remove($fileToRemove);
    }

    public function createFile(string $file, string $content): void
    {
        $file = u($file)->ensureStart($this->directoryOrFile)->toString();

        $this->filesystem->dumpFile($file, $content);
    }

    public function copyFile(string $source, string $destination): void
    {
        $source = u($source)->ensureStart($this->directoryOrFile)->toString();
        $destination = u($destination)->ensureStart($this->directoryOrFile)->toString();

        $this->filesystem->copy($source, $destination);
    }

    public function copyDirectory(string $source, string $destination): void
    {
        $source = u($source)->ensureStart($this->directoryOrFile)->toString();
        $destination = u($destination)->ensureStart($this->directoryOrFile)->toString();

        $this->filesystem->mirror($source, $destination);
    }

    public function moveFile(string $source, string $destination): void
    {
        $source = u($source)->ensureStart($this->directoryOrFile)->toString();
        $destination = u($destination)->ensureStart($this->directoryOrFile)->toString();

        $this->filesystem->rename($source, $destination);
    }

    public function moveDirectory(string $source, string $destination): void
    {
        $source = u($source)->ensureStart($this->directoryOrFile)->toString();
        $destination = u($destination)->ensureStart($this->directoryOrFile)->toString();

        $this->filesystem->rename($source, $destination);
    }

    public function renameFile(string $source, string $destination): void
    {
        $source = u($source)->ensureStart($this->directoryOrFile)->toString();
        $destination = u($destination)->ensureStart($this->directoryOrFile)->toString();

        $this->filesystem->rename($source, $destination);
    }

    public function renameDirectory(string $source, string $destination): void
    {
        $source = u($source)->ensureStart($this->directoryOrFile)->toString();
        $destination = u($destination)->ensureStart($this->directoryOrFile)->toString();

        $this->filesystem->rename($source, $destination);
    }

    public function exists(): bool
    {
        return $this->filesystem->exists($this->directoryOrFile);
    }

    public function isFile(): bool
    {
        return \is_file($this->directoryOrFile);
    }

    public function isDirectory(): bool
    {
        return \is_dir($this->directoryOrFile);
    }

    /**
     * @throws \Exception
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * @throws \Exception
     */
    public function isEmpty(): bool
    {
        return 0 === $this->countFiles()
            && 0 === $this->countDirectories();
    }

    /**
     * @throws \Exception
     */
    public function countFiles(): int
    {
        return $this->listFiles()->count();
    }

    /**
     * @return SplFileInfoCollection
     * @throws \Exception
     */
    public function listFiles(): SplFileInfoCollection
    {
        $finder = $this->finder->files()->in($this->directoryOrFile);

        return SplFileInfoCollection::fromFinder($finder);
    }

    /**
     * @throws \Exception
     */
    public function countDirectories(): int
    {
        return $this->listDirectories()->count();
    }

    /**
     * @return SplFileInfoCollection
     * @throws \Exception
     */
    public function listDirectories(): SplFileInfoCollection
    {
        $finder = $this->finder->directories()->in($this->directoryOrFile);

        return SplFileInfoCollection::fromFinder($finder);
    }

    public function isReadable(): bool
    {
        return \is_readable($this->directoryOrFile);
    }

    public function isWritable(): bool
    {
        return \is_writable($this->directoryOrFile);
    }

    public function isExecutable(): bool
    {
        return \is_executable($this->directoryOrFile);
    }

    public function isLink(): bool
    {
        return \is_link($this->directoryOrFile);
    }
}
