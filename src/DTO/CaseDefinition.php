<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO;

final class CaseDefinition
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
            strtoupper($data['name']),
            $data['value'],
        );
    }

    public function valueIsNull(): bool
    {
        return null === $this->value;
    }
}
