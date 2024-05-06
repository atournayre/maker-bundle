<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Config;

use Nette\PhpGenerator\Attribute;
use Webmozart\Assert\Assert;

class AddAttributeMakerConfiguration extends MakerConfiguration
{
    private string $serviceNamespace;
    private array $attributes = [];

    public function serviceNamespace(): string
    {
        return $this->serviceNamespace;
    }

    public function withServiceNamespace(string $serviceNamespace): self
    {
        $config = clone $this;
        $config->serviceNamespace = $serviceNamespace;
        return $config;
    }

    public function attributes(): array
    {
        return $this->attributes;
    }

    public function withAttributes(array $attributes): self
    {
        Assert::allNullOrIsInstanceOf($attributes, Attribute::class, 'The attributes must be an array of Nette\PhpGenerator\Attribute or null.');

        $config = clone $this;
        $config->attributes = $attributes;
        return $config;
    }
}
