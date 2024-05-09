<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Collection\DecimalValueCollection;
use Atournayre\Collection\TypedCollectionImmutable;
use Atournayre\Collection\TypedCollection;
use Atournayre\Types\DecimalValue;
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
            ->setComments($this->comments($makerConfiguration))
        ;
    }

    private function extendsClass(CollectionMakerConfiguration $collectionMakerConfiguration): string
    {
        if ($collectionMakerConfiguration->ofDecimals()) {
            return DecimalValueCollection::class;
        }

        return $collectionMakerConfiguration->isImmutable()
            ? TypedCollectionImmutable::class
            : TypedCollection::class;
    }

    private function collectionType(CollectionMakerConfiguration $collectionMakerConfiguration): string
    {
        if ($collectionMakerConfiguration->ofDecimals()) {
            return DecimalValue::class;
        }

        return $collectionMakerConfiguration->relatedObject();
    }

    private function propertyType(CollectionMakerConfiguration $collectionMakerConfiguration): Property
    {
        $type = $this->collectionType($collectionMakerConfiguration);

        return (new Property('type'))
            ->setVisibility('protected')
            ->setStatic()
            ->setType('string')
            ->setValue(new Literal(Str::classNameSemiColonFromNamespace($type)))
        ;
    }

    /**
     * @return string[]
     */
    private function comments(CollectionMakerConfiguration $collectionMakerConfiguration): array
    {
        $extendsTypeShortName = Str::classNameFromNamespace($this->extendsClass($collectionMakerConfiguration), '');
        $collectionTypeShortName = Str::classNameFromNamespace($this->collectionType($collectionMakerConfiguration), '');

        return [
            '@extends '.$extendsTypeShortName.'<'.$collectionTypeShortName.'>',
            '',
            '@method ' . $collectionMakerConfiguration->classname() . ' add(' . $collectionTypeShortName . ' $value)',
            '@method ' . $collectionTypeShortName . '[] values()',
            '@method ' . $collectionTypeShortName . ' first()',
            '@method ' . $collectionTypeShortName . ' last()',
        ];
    }
}
