<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Aimeos\Map;
use Atournayre\Bundle\MakerBundle\DTO\CaseDefinition;
use Nette\PhpGenerator\EnumCase;

class EnumMakerConfiguration extends MakerConfiguration
{
    protected static function classNameSuffix(): string
    {
        return 'Enum';
    }

    /**
     * @var array|CaseDefinition[] $cases
     */
    private array $cases = [];

    /**
     * @return array|CaseDefinition[] $cases
     */
    public function cases(): array
    {
        return $this->cases;
    }

    /**
     * @param array|CaseDefinition[] $cases
     *
     * @return $this
     */
    public function withCases(array $cases): self
    {
        $config = clone $this;
        $config->cases = $cases;
        return $config;
    }

    public function hasCases(): bool
    {
        return $this->cases !== [];
    }

    public function hasNoCases(): bool
    {
        return !$this->hasCases();
    }

    public function isBackedEnum(): bool
    {
        if ($this->hasNoCases()) {
            return false;
        }

        return !$this->firstCase()->valueIsNull();
    }

    private function firstCase(): CaseDefinition
    {
        return current($this->cases());
    }

    public function isPureEnum(): bool
    {
        return !$this->isBackedEnum();
    }

    public function enumType(): ?string
    {
        if ($this->hasNoCases()) {
            return null;
        }

        $value = $this->firstCase()->value;

        if (is_numeric($value)) {
            return 'int';
        }

        if (is_string($value)) {
            return 'string';
        }

        return null;
    }

    /**
     * @return array|EnumCase[]
     */
    public function enumCases(): array
    {
        return Map::from($this->cases())
            ->map(static function (CaseDefinition $case) {
                $enumCase = new EnumCase($case->name);

                if ($case->valueIsNull()) {
                    return $enumCase;
                }

                return $enumCase->setValue($case->value);
            })
            ->toArray();
    }
}
