<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Atournayre\Bundle\MakerBundle\Config\ControllerMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Method;

class ControllerBuilder extends FromTemplateBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === ControllerMakerConfiguration::class;
    }

    /**
     * @param ControllerMakerConfiguration $makerConfiguration
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $phpFileDefinition = parent::createPhpFileDefinition($makerConfiguration);
        $phpFileDefinition->renameClass($makerConfiguration->classname());
        $phpFileDefinition->updateMethod('createVo', $this->updateVoMethod($phpFileDefinition->getMethod('createVo'), $makerConfiguration));
        $phpFileDefinition->updateMethod('createForm', $this->updateFormMethod($phpFileDefinition->getMethod('createForm'), $makerConfiguration));
        $phpFileDefinition->addUse($makerConfiguration->entityNamespace());
        $phpFileDefinition->addUse($makerConfiguration->formTypeNamespace());
        $phpFileDefinition->addUse($makerConfiguration->voNamespace());
        $phpFileDefinition->removeUse(FormType::class);
        return $phpFileDefinition;
    }

    private function updateVoMethod(
        Method $method,
        ControllerMakerConfiguration $controllerMakerConfiguration
    ): Method
    {
        $entityClassName = $controllerMakerConfiguration->entityClassName();
        $voNamespace = $controllerMakerConfiguration->voNamespace();
        $voClassName = $controllerMakerConfiguration->voClassName();

        $method->setComment('@param '.$entityClassName.' $entity');
        $method->setReturnType($voNamespace);
        $method->setBody('return '.$voClassName.'::create($entity);');
        return $method;
    }

    private function updateFormMethod(
        Method $method,
        ControllerMakerConfiguration $controllerMakerConfiguration
    ): Method
    {
        $voClassName = $controllerMakerConfiguration->voClassName();
        $method->setComment('@param ' . $voClassName . '|null $data');

        // Replace FormType by the form type class name in the body
        $formTypeNamespace = $controllerMakerConfiguration->formTypeNamespace();
        $formType = FormType::class;
        $body = $method->getBody();
        $body = Str::replace($body, $formType, $formTypeNamespace);

        $method->setBody($body);
        return $method;
    }
}
