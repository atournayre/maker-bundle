<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO\Config;

final class Directories
{
    public function __construct(
        public readonly ?string $controller = null,
        public readonly ?string $entity = null,
        public readonly ?string $form = null,
        public readonly ?string $vo = null,
    )
    {
    }

    /**
     * @param array<string, string> $directories
     */
    public static function fromArray(array $directories): self
    {
        return new self(
            $directories['controller'] ?? null,
            $directories['entity'] ?? null,
            $directories['form'] ?? null,
            $directories['vo'] ?? null,
        );
    }
}
