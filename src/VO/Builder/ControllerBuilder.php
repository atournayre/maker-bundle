<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\ClassType;
use Webmozart\Assert\Assert;

class ControllerBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self|FromTemplateBuilder
    {
        $hasForm = $fileDefinition->configuration()->hasExtraProperty('formTypePath');

        if ($hasForm) {
            return self::buildWithForm($fileDefinition);
        }

        return self::buildSimple($fileDefinition);
    }

    private static function buildWithForm(FileDefinition $fileDefinition): self|FromTemplateBuilder
    {
        $config = $fileDefinition->configuration();
        $rootNamespace = $config->rootNamespace();
        $rootDir = $config->rootDir();

        $config = $config
            ->withExtraProperty(
                'entityClassName',
                Str::prefixByRootNamespace(Str::namespaceFromPath($config->getExtraProperty('entityPath'), $rootDir), $rootNamespace)
            )
            ->withExtraProperty(
                'formTypeClassName',
                Str::prefixByRootNamespace(Str::namespaceFromPath($config->getExtraProperty('formTypePath'), $rootDir), $rootNamespace)
            )
            ->withExtraProperty(
                'voClassName',
                Str::prefixByRootNamespace(Str::namespaceFromPath($config->getExtraProperty('voPath'), $rootDir), $rootNamespace)
            )
        ;

        $fileDefinition = $fileDefinition->withConfiguration($config);

        $self = FromTemplateBuilder::build($fileDefinition);

        $self = (new self($fileDefinition))
            ->createFromCode((string)$self->file)
            ->changeClassName($fileDefinition->fullName());

        $class = $self->getClass();

        self::updateMethodCreateVo($class, $fileDefinition);
        self::updateMethodCreateForm($class, $fileDefinition);

        return $self
            ->withUse($config->getExtraProperty('entityClassName'))
            ->withUse($config->getExtraProperty('formTypeClassName'))
            ->withUse($config->getExtraProperty('voClassName'))
            ->removeUse(\Symfony\Component\Form\Extension\Core\Type\FormType::class)
        ;
    }

    private static function updateMethodCreateVo(ClassType $class, FileDefinition $fileDefinition): void
    {
        $config = $fileDefinition->configuration();
        Assert::true($config->hasExtraProperty('entityClassName'), 'The entityClassName extra property is missing');
        Assert::true($config->hasExtraProperty('voClassName'), 'The voClassName extra property is missing');

        $entityClassName = $config->getExtraProperty('entityClassName');
        $voClassName = $config->getExtraProperty('voClassName');

        $methodCreateVo = $class->getMethod('createVO');

        // Replace EntityNamespace by the entity class name in the phpDoc
        $comment = $methodCreateVo->getComment();
        $comment = Str::replace($comment, 'EntityNamespace', $entityClassName);
        $comment = Str::replace($comment, '@return mixed', Str::sprintf('@return %s', $voClassName));
        $methodCreateVo->setComment($comment);
        $methodCreateVo->setReturnType($voClassName);

        // Replace VoNamespace by the vo class name in the body
        $methodCreateVo->setBody('return '.$voClassName.'::create($entity);');
    }

    private static function updateMethodCreateForm(ClassType $class, FileDefinition $fileDefinition): void
    {
        $config = $fileDefinition->configuration();
        $formTypeNamespace = $config->getExtraProperty('formTypeClassName');
        $voClassName = $config->getExtraProperty('voClassName');

        $formType = \Symfony\Component\Form\Extension\Core\Type\FormType::class;

        // Replace FormType by the form type class name in the body
        $methodCreateForm = $class->getMethod('createForm');
        $body = $methodCreateForm->getBody();
        $body = Str::replace($body, $formType, $formTypeNamespace);
        $methodCreateForm->setBody($body);

        $methodCreateForm->addComment('@param ' . $voClassName . '|null $data');
    }

    private static function buildSimple(FileDefinition $fileDefinition): self|FromTemplateBuilder
    {
        $self = FromTemplateBuilder::build($fileDefinition);

        return (new self($fileDefinition))
            ->createFromCode((string)$self->file)
            ->changeClassName($fileDefinition->fullName())
        ;
    }
}
