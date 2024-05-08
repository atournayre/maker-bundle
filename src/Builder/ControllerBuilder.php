<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Atournayre\Bundle\MakerBundle\Config\ControllerMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Method;

class ControllerBuilder extends FromTemplateBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === ControllerMakerConfiguration::class;
    }

    public function createPhpFileDefinition(MakerConfigurationInterface|ControllerMakerConfiguration $makerConfiguration): PhpFileDefinition
    {
        $phpFileDefinition = parent::createPhpFileDefinition($makerConfiguration);
        $phpFileDefinition->renameClass($makerConfiguration->classname());
        $phpFileDefinition->updateMethod('createVo', $this->updateVoMethod($phpFileDefinition->getMethod('createVo'), $makerConfiguration));
        $phpFileDefinition->updateMethod('createForm', $this->updateFormMethod($phpFileDefinition->getMethod('createForm'), $makerConfiguration));
        $phpFileDefinition->addUse($makerConfiguration->entityClassName());
        $phpFileDefinition->addUse($makerConfiguration->formTypeClassName());
        $phpFileDefinition->addUse($makerConfiguration->voClassName());
        $phpFileDefinition->removeUse(\Symfony\Component\Form\Extension\Core\Type\FormType::class);
        return $phpFileDefinition;
    }

    private function updateVoMethod(
        Method $method,
        MakerConfigurationInterface|ControllerMakerConfiguration $makerConfiguration
    ): Method
    {
        $entityClassName = Str::classNameFromNamespace($makerConfiguration->entityClassName(), '');
        $voNamespace = $makerConfiguration->voClassName();
        $voClassName = Str::classNameFromNamespace($voNamespace, '');

        $method->setComment('@param '.$entityClassName.' $entity');
        $method->setReturnType($voNamespace);
        $method->setBody('return '.$voClassName.'::create($entity);');
        return $method;
    }

    private function updateFormMethod(
        Method $method,
        MakerConfigurationInterface|ControllerMakerConfiguration $makerConfiguration
    ): Method
    {
        $voClassName = Str::classNameFromNamespace($makerConfiguration->voClassName(), '');
        $method->setComment('@param ' . $voClassName . '|null $data');

        // Replace FormType by the form type class name in the body
        $formTypeNamespace = $makerConfiguration->formTypeClassName();
        $formType = \Symfony\Component\Form\Extension\Core\Type\FormType::class;
        $body = $method->getBody();
        $body = Str::replace($body, $formType, $formTypeNamespace);
        $method->setBody($body);
        return $method;
    }
}
