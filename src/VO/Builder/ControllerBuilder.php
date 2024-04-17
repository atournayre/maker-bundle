<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\ClassType;

class ControllerBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self|FromTemplateBuilder
    {
        $hasForm = $fileDefinition->configuration()->hasExtraProperty('formType');

        if ($hasForm) {
            return self::buildWithForm($fileDefinition);
        }

        return self::buildSimple($fileDefinition);
    }

    private static function buildWithForm(FileDefinition $fileDefinition): self|FromTemplateBuilder
    {
        $config = $fileDefinition->configuration();

        $config = $config
            ->withExtraProperty('entity', '\\'.$config->getExtraProperty('entity'))
            ->withExtraProperty('formType', $config->getExtraProperty('formType'))
            ->withExtraProperty('vo', '\\'.$config->getExtraProperty('vo'))
            ->withExtraProperty('entityClassName', Str::classNameFromNamespace($config->getExtraProperty('entity'), ''))
            ->withExtraProperty('voClassName', Str::classNameFromNamespace($config->getExtraProperty('vo'), ''))
        ;

        $self = FromTemplateBuilder::build($fileDefinition);

        $self = (new self($fileDefinition))
            ->createFromCode((string)$self->file)
            ->changeClassName($fileDefinition->fullName());

        $class = $self->getClass();

        self::updateMethodCreateVo($class, $fileDefinition);
        self::updateMethodCreateForm($class, $fileDefinition);

        return $self
            ->withUse($config->getExtraProperty('entity'))
            ->withUse($config->getExtraProperty('formType'))
            ->withUse($config->getExtraProperty('vo'))
            ->removeUse(\Symfony\Component\Form\Extension\Core\Type\FormType::class)
        ;
    }

    private static function updateMethodCreateVo(ClassType $class, FileDefinition $fileDefinition): void
    {
        $config = $fileDefinition->configuration();
        $entityClassName = $config->getExtraProperty('entityClassName');
        $voClassName = $config->getExtraProperty('voClassName');
        $voNamespace = $config->getExtraProperty('vo');

        $methodCreateVo = $class->getMethod('createVO');

        // Replace EntityNamespace by the entity class name in the phpDoc
        $comment = $methodCreateVo->getComment();
        $comment = Str::replace($comment, 'EntityNamespace', $entityClassName);
        $comment = Str::replace($comment, '@return mixed', Str::sprintf('@return %s', $voClassName));
        $methodCreateVo->setComment($comment);
        $methodCreateVo->setReturnType($voNamespace);

        // Replace VoNamespace by the vo class name in the body
        $methodCreateVo->setBody('return '.$voClassName.'::create($entity);');
    }

    private static function updateMethodCreateForm(ClassType $class, FileDefinition $fileDefinition): void
    {
        $config = $fileDefinition->configuration();
        $formTypeNamespace = $config->getExtraProperty('formType');
        $voClassName = Str::classNameFromNamespace($config->getExtraProperty('vo'), '');

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
