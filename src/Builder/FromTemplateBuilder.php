<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\FromTemplateMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Config\InterfaceMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;

final class FromTemplateBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === FromTemplateMakerConfiguration::class;
    }

    private function comments(?string $comment): array
    {
        $defaultComments = [
            'This file has been auto-generated',
            '',
        ];

        if (null === $comment || '' === $comment) {
            return array_filter($defaultComments);
        }

        return [
            ...$defaultComments,
            ...explode(PHP_EOL, $comment),
        ];
    }

    public function createInstance(MakerConfigurationInterface|InterfaceMakerConfiguration $makerConfiguration): PhpFileDefinition
    {
        $sourceCode = $makerConfiguration->sourceCode();

        $phpFile = PhpFile::fromCode($sourceCode);
        $namespace = current($phpFile->getNamespaces());
        $class = current($namespace->getClasses());
        $uses = array_flip($namespace->getUses());
        $usesFunction = array_flip($namespace->getUses(PhpNamespace::NameFunction));

        $comments = $this->comments($class->getComment());

        $phpFileDefinition = parent::createInstance($makerConfiguration)
            ->setStrictTypes($phpFile->hasStrictTypes());

        if ($class->isInterface()) {
            $phpFileDefinition->setInterface();
        } elseif ($class->isTrait()) {
            $phpFileDefinition->setTrait();
        }

        if (!$class->isTrait() && [] !== ($class->getExtends() ?? [])) {
            $extends = is_array($class->getExtends()) ? current($class->getExtends()) : $class->getExtends();
            $phpFileDefinition->setExtends($extends);
        }

        $phpFileDefinition
            ->setConstants($class->getConstants() ?? [])
            ->setMethods($class->getMethods() ?? [])
        ;

        if ($phpFileDefinition->isClass()) {
            $phpFileDefinition
                ->setFinal($class->isFinal())
                ->setReadonly($class->isReadOnly())
                ->setAbstract($class->isAbstract())
                ->setImplements($class->getImplements() ?? [])
            ;
        }

        if (!$phpFileDefinition->isInterface()) {
            $phpFileDefinition
                ->setProperties($class->getProperties())
                ->setTraits($class->getTraits() ?? [])
            ;
        }

        $phpFileDefinition
            ->setComments($comments)
            ->setUses($uses)
            ->setUsesFunctions($usesFunction)
            ->setAttributes($class->getAttributes() ?? [])
        ;

        return $phpFileDefinition;
    }
}
