<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO\Config;

final class Directories
{
    public function __construct(
        public readonly ?string $entity = null,
        public readonly ?string $form = null,
        public readonly ?string $vo = null,
    )
    {
    }

    public static function fromArray(array $directories): self
    {
        return new self(
            $directories['entity'] ?? null,
            $directories['form'] ?? null,
            $directories['vo'] ?? null,
        );
    }
}
