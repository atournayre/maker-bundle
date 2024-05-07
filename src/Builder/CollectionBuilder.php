<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\CollectionMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Property;

final class CollectionBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === CollectionMakerConfiguration::class;
    }

    public function createInstance(MakerConfigurationInterface|CollectionMakerConfiguration $makerConfiguration): PhpFileDefinition
    {
        $extends = $this->extendsClass($makerConfiguration);
        $collectionType = $this->collectionType($makerConfiguration);

        return parent::createInstance($makerConfiguration)
            ->setExtends($extends)
            ->setUses([
                $extends,
                $collectionType
            ])
            ->setProperties([
                $this->propertyType($makerConfiguration),
            ])
        ;
    }

    private function extendsClass(MakerConfigurationInterface|CollectionMakerConfiguration $makerConfiguration): string
    {
        if ($makerConfiguration->ofDecimals()) {
            return \Atournayre\Collection\DecimalValueCollection::class;
        }

        return $makerConfiguration->isImmutable()
            ? \Atournayre\Collection\TypedCollectionImmutable::class
            : \Atournayre\Collection\TypedCollection::class;
    }

    private function collectionType(MakerConfigurationInterface|CollectionMakerConfiguration $makerConfiguration): string
    {
        if ($makerConfiguration->ofDecimals()) {
            return \Atournayre\Types\DecimalValue::class;
        }

        return $makerConfiguration->relatedObject();
    }

    private function propertyType(MakerConfigurationInterface|CollectionMakerConfiguration $makerConfiguration): Property
    {
        $type = $this->collectionType($makerConfiguration);

        return (new Property('type'))
            ->setVisibility('protected')
            ->setStatic()
            ->setType('string')
            ->setValue(new Literal(Str::classNameSemiColonFromNamespace($type)))
        ;
    }
}
