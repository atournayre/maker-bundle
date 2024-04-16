<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

class CollectionBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self|FromTemplateBuilder
    {
        $config = $fileDefinition->configuration();
        Assert::true($config->hasExtraProperty('collectionRelatedObject'), 'The collectionRelatedObject property is required');
        Assert::true($config->hasExtraProperty('collectionIsImmutable'), 'The collectionIsImmutable property is required');

        $extends = $config->getExtraProperty('collectionIsImmutable')
            ? \Atournayre\Collection\TypedCollectionImmutable::class
            : \Atournayre\Collection\TypedCollection::class;
        $type = Str::prefixByRootNamespace($config->getExtraProperty('collectionRelatedObject'), $config->rootNamespace());

        $file = new PhpFile;
        $file->addComment('This file has been auto-generated');
        $file->setStrictTypes();
        $file->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setExtends($extends)
        ;

        $property = new Property('type');
        $property
            ->setVisibility('protected')
            ->setStatic()
            ->setType('string')
            ->setValue(new Literal(Str::classNameSemiColonFromNamespace($type)))
        ;

        $self = (new self($fileDefinition))
            ->withFile($file);

        $class = $self->getClass();

        $class->addMember($property);

        return ($self)
            ->withUse($extends)
            ->withUse($type)
        ;
    }
}
