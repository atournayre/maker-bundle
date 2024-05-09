<?php
declare(strict_types=1);

namespace App\VO\File;

class FileSize
{
    public const KB = 1024;

    private function __construct(private readonly int $bytes)
    {
    }

    public static function fromBytes(int $bytes): self
    {
        return new self($bytes);
    }

    public function asIs(): int
    {
        return $this->bytes;
    }

    public function inKilobytes(): float
    {
        return $this->bytes / self::KB;
    }

    public function inMegabytes(): float
    {
        return $this->inKilobytes() / self::KB;
    }

    public function inGigabytes(): float
    {
        return $this->inMegabytes() / self::KB;
    }

    public function inTerabytes(): float
    {
        return $this->inGigabytes() / self::KB;
    }

    public function humanReadable(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $value = $this->bytes;
        $unit = 0;

        while ($value >= self::KB && $unit < count($units) - 1) {
            $value /= self::KB;
            ++$unit;
        }

        return round($value, 2) . ' ' . $units[$unit];
    }
}
