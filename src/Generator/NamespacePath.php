<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

final class NamespacePath
{
    private string $relativePath;
    private string $rootNamespace;

    public static function normalize(string $relativePath): string
    {
        return str_replace('-', '', ucwords(trim($relativePath, '/'), '/-'));
    }

    public function __construct(string $relativePath, string $rootNamespace = 'App')
    {
        $this->relativePath = self::normalize($relativePath);
        $this->rootNamespace = $rootNamespace;
    }

    public function normalizedValue(): string
    {
        return $this->relativePath;
    }

    public function hasNamespace(): bool
    {
        return false !== strpos($this->relativePath, '/');
    }

    public function toNamespace(string $suffixNamespace = ''): string
    {
        if ('' === $this->rootNamespace) {
            return str_replace('/', '\\', $this->relativePath).$suffixNamespace;
        }

        return $this->rootNamespace.'\\'.str_replace('/', '\\', $this->relativePath).$suffixNamespace;
    }

    public function toShortClassName(): string
    {
        if (false === $position = strrpos($this->relativePath, '/')) {
            return $this->relativePath;
        }

        return substr($this->relativePath, $position + 1);
    }
}
