<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use App\Contracts\Service\FailFastInterface;
use App\Contracts\Service\PostConditionsChecksInterface;
use App\Contracts\Service\PreConditionsChecksInterface;
use App\Contracts\Service\TagQueryServiceInterface;
use App\Exception\FailFast;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;

class ServiceQueryBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $config = $fileDefinition->configuration();
        $voParameter = Str::prefixByRootNamespace($config->getExtraProperty('vo'), $config->rootNamespace());

        $file = new PhpFile;
        $file->addComment('This file has been auto-generated');
        $file->setStrictTypes();
        $file->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setReadOnly()
        ;

        return (new self($fileDefinition))
            ->withFile($file)
            ->withUse(FailFast::class)
            ->withUse(\Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag::class)
            ->withUse(\App\Contracts\Service\TagQueryServiceInterface::class)
            ->withUse($voParameter)
            ->withUse(PreConditionsChecksInterface::class)
            ->withUse(FailFastInterface::class)
            ->withUse(TagQueryServiceInterface::class)
            ->withUse(PostConditionsChecksInterface::class)
            ->withUse(\App\VO\Context::class)
            ->withAttributes()
            ->withImplementationOfInterface(PreConditionsChecksInterface::class, $voParameter)
            ->withImplementationOfInterface(FailFastInterface::class, $voParameter)
            ->withImplementationOfInterface(TagQueryServiceInterface::class, $voParameter)
            ->withImplementationOfInterface(PostConditionsChecksInterface::class, $voParameter)
            ->withInvoke()
        ;
    }

    private function withAttributes(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $attributes = [
            new Attribute(\Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag::class, [
                new Literal('TagQueryServiceInterface::class'),
            ]),
        ];

        $class->setAttributes($attributes);

        return $clone;
    }

    private function withInvoke(): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $method = (new Method('__invoke'))
            ->setPrivate()
            ->addComment('This service is not meant to be used directly');

        $class->addMember($method);

        return $clone;
    }

    private function withImplementationOfInterface(string $interface, string $objectType): self
    {
        $clone = clone $this;
        $class = $clone->getClass();

        $class->addImplement($interface);

        /** @var ClassType $sourceInterface */
        $sourceInterface = InterfaceType::from($interface);

        foreach ($sourceInterface->getMethods() as $method) {
            $class->addMember($this->implementMethod($method->getName(), $sourceInterface, $objectType));
        }

        return $clone;
    }

    private function implementMethod(string $method, $sourceInterface, string $objectType): Method
    {
        $sourceMethod = $sourceInterface->getMethod($method);
        return (new Method($sourceMethod->getName()))
            ->setPublic()
            ->addComment($sourceMethod->getComment())
            ->addComment('@param '.Str::classNameFromNamespace($objectType, '').' $object')
            ->setReturnType($sourceMethod->getReturnType())
            ->setParameters($sourceMethod->getParameters());
    }
}
