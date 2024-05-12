<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO;

use function Symfony\Component\String\u;

final class CaseDefinition implements \Stringable
{
    private function __construct(
        public string $name,
        public ?string $value,
    )
    {
    }

    /**
     * @param array{name: string, value: ?string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            u($data['name'])->upper()->toString(),
            $data['value'],
        );
    }

    public function valueIsNull(): bool
    {
        return null === $this->value;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
