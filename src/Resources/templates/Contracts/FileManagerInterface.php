<?php
declare(strict_types=1);

namespace App\Contracts;

use App\Collection\SplFileInfoCollection;

interface FileManagerInterface
{
    public static function from(string $directoryOrFile): self;

    public function createDirectory(string $directory): void;

    public function removeDirectory(string $directory): void;

    public function removeFile(string $file): void;

    public function createFile(string $file, string $content): void;

    public function copyFile(string $source, string $destination): void;

    public function copyDirectory(string $source, string $destination): void;

    public function moveFile(string $source, string $destination): void;

    public function moveDirectory(string $source, string $destination): void;

    public function renameFile(string $source, string $destination): void;

    public function renameDirectory(string $source, string $destination): void;

    public function exists(): bool;

    public function isFile(): bool;

    public function isDirectory(): bool;

    public function isNotEmpty(): bool;

    public function isEmpty(): bool;

    public function countFiles(): int;

    public function listFiles(): SplFileInfoCollection;

    public function countDirectories(): int;

    public function listDirectories(): SplFileInfoCollection;

    public function isReadable(): bool;

    public function isWritable(): bool;

    public function isExecutable(): bool;

    public function isLink(): bool;

}
