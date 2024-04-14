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
        $self = FromTemplateBuilder::build($fileDefinition);

        // Get the namespace of the new file from file definition
        $newNamespace = $fileDefinition->fullName();

        $namespace = $self->file->getNamespaces()[$fileDefinition->namespace()];

        $classes = $namespace->getClasses();
        $classesKeys = array_keys($classes);
        $identifierForTemplateClass = current($classesKeys);

        /** @var ClassType $newClass */
        $newClass = clone $classes[$identifierForTemplateClass];
        $newClass->setName(Str::classNameFromNamespace($newNamespace, ''));

        $namespace->add($newClass);
        $namespace->removeClass($identifierForTemplateClass);

        $self = $self
            ->withFileDefinition($self->fileDefinition->withSourceCode((string)$self->file));

        $class = $self->getClass();

        $config = $fileDefinition->configuration();

        $entityNamespace = '\\'.$config->getExtraProperty('entity');
        $formTypeNamespace = $config->getExtraProperty('formType');
        $voNamespace = '\\'.$config->getExtraProperty('vo');

        $entityClassName = Str::classNameFromNamespace($entityNamespace, '');
        $voClassName = Str::classNameFromNamespace($voNamespace, '');

        $methodCreateVo = $class->getMethod('createVO');

        // Replace EntityNamespace by the entity class name in the phpDoc
        $comment = $methodCreateVo->getComment();
        $comment = Str::replace($comment, 'EntityNamespace', $entityClassName);
        $comment = Str::replace($comment, '@return mixed', Str::sprintf('@return %s', $voClassName));
        $methodCreateVo->setComment($comment);
        $methodCreateVo->setReturnType($voNamespace);

        // Replace VoNamespace by the vo class name in the body
        $methodCreateVo->setBody('return '.$voClassName.'::create($entity);');

        $formType = \Symfony\Component\Form\Extension\Core\Type\FormType::class;
        $namespace->removeUse($formType);

        // Replace FormType by the form type class name in the body
        $methodCreateForm = $class->getMethod('createForm');
        $body = $methodCreateForm->getBody();
        $body = Str::replace($body, $formType, $formTypeNamespace);
        $methodCreateForm->setBody($body);

        $methodCreateForm->addComment('@param '.$voClassName.'|null $data');

        return $self
            ->withUse($entityNamespace)
            ->withUse($formTypeNamespace)
            ->withUse($voNamespace)
            ;
    }
}
