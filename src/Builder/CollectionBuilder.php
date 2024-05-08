<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\CollectionMakerConfiguration;
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

    /**
     * @param CollectionMakerConfiguration $makerConfiguration
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $extends = $this->extendsClass($makerConfiguration);
        $collectionType = $this->collectionType($makerConfiguration);

        return parent::createPhpFileDefinition($makerConfiguration)
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

    private function extendsClass(CollectionMakerConfiguration $makerConfiguration): string
    {
        if ($makerConfiguration->ofDecimals()) {
            return \Atournayre\Collection\DecimalValueCollection::class;
        }

        return $makerConfiguration->isImmutable()
            ? \Atournayre\Collection\TypedCollectionImmutable::class
            : \Atournayre\Collection\TypedCollection::class;
    }

    private function collectionType(CollectionMakerConfiguration $makerConfiguration): string
    {
        if ($makerConfiguration->ofDecimals()) {
            return \Atournayre\Types\DecimalValue::class;
        }

        return $makerConfiguration->relatedObject();
    }

    private function propertyType(CollectionMakerConfiguration $makerConfiguration): Property
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
