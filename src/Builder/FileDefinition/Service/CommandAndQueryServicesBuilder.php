<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service;

use App\Attribute\CommandService;
use App\Attribute\QueryService;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinition\InterfaceBuilder;
use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use App\Contracts\Logger\LoggerInterface;
use App\Contracts\Service\CommandServiceInterface;
use App\Contracts\Service\FailFastInterface;
use App\Contracts\Service\PostConditionsChecksInterface;
use App\Contracts\Service\PreConditionsChecksInterface;
use App\Contracts\Service\QueryServiceInterface;
use App\Contracts\Service\TagCommandServiceInterface;
use App\Contracts\Service\TagQueryServiceInterface;
use App\Exception\FailFast;
use App\Helper\AttributeHelper;
use Nette\PhpGenerator\Literal;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Webmozart\Assert\Assert;

class CommandAndQueryServicesBuilder implements FileDefinitionBuilderInterface
{
    public static function filesDefinitions(MakerConfig $config): array
    {
        return [
            self::buildAttributeCommandService('Attribute', 'CommandService', $config),
            self::buildAttributeQueryService('Attribute', 'QueryService', $config),
            self::buildInterfaceFailFast('Contracts\\Service', 'FailFast', $config),
            self::buildInterfacePostConditionsChecks('Contracts\\Service', 'PostConditionsChecks', $config),
            self::buildInterfacePreConditionsChecks('Contracts\\Service', 'PreConditionsChecks', $config),
            self::buildInterfaceTagCommandService('Contracts\\Service', 'TagCommandService', $config),
            self::buildInterfaceTagQueryService('Contracts\\Service', 'TagQueryService', $config),
            self::buildInterfaceCommandService('Contracts\\Service', 'CommandService', $config),
            self::buildInterfaceQueryService('Contracts\\Service', 'QueryService', $config),
            self::buildHelperAttribute('Helper', 'Attribute', $config),
            self::buildServiceCommand('Service', 'CommandService', $config),
            self::buildServiceQuery('Service', 'QueryService', $config),
        ];
    }

    /**
     * @throws \Exception
     */
    #[\Override] public static function build(MakerConfig $config, string $namespace = '', string $name = ''): FileDefinitionBuilder
    {
        throw new \Exception('Use filesDefinitions() instead.');
    }


    public static function buildAttributeCommandService(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $fileDefinition->file->addClass($fileDefinition->fullName())
            ->addAttribute('Attribute', [
                new Literal('\Attribute::TARGET_CLASS'),
            ]);

        $class = $fileDefinition->getClass();

        $class->addMethod('__construct')
            ->addParameter('serviceName')
            ->setType('string');

        $class->addProperty('serviceName')
            ->setPublic()
            ->setType('string')
            ->setReadOnly();

        return $fileDefinition;
    }

    public static function buildAttributeQueryService(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $fileDefinition->file->addClass($fileDefinition->fullName())
            ->addAttribute('Attribute', [
                new Literal('\Attribute::TARGET_CLASS'),
            ]);

        $class = $fileDefinition->getClass();

        $class->addMethod('__construct')
            ->addParameter('serviceName')
            ->setType('string');

        $class->addProperty('serviceName')
            ->setPublic()
            ->setType('string')
            ->setReadOnly();

        return $fileDefinition;
    }

    public static function buildInterfaceFailFast(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $class = $fileDefinition->getClass();

        $namespace = $class->getNamespace();
        $namespace->addUse(\App\VO\Context::class);
        $namespace->addUse(FailFast::class);

        $class->addMethod('failFast')
            ->setPublic()
            ->setReturnType('void')
            ->addParameter('object');

        $class->getMethod('failFast')
            ->addParameter('context')
            ->setType(\App\VO\Context::class);

        $class->getMethod('failFast')
            ->addComment('Implement logic here, or remove method and interface from the class if not needed.')
            ->addComment('@throws FailFast');

        return $fileDefinition;
    }

    public static function buildInterfacePostConditionsChecks(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $class = $fileDefinition->getClass();

        $namespace = $class->getNamespace();
        $namespace->addUse(\App\VO\Context::class);

        $class->addMethod('postConditionsChecks')
            ->setPublic()
            ->setReturnType('void')
            ->addParameter('object');

        $class->getMethod('postConditionsChecks')
            ->addParameter('context')
            ->setType(\App\VO\Context::class);

        $class->getMethod('postConditionsChecks')
            ->addComment('Use assertions, or remove method and interface from the class if not needed.')
            ->addComment('@throws \Exception');

        return $fileDefinition;
    }

    public static function buildInterfacePreConditionsChecks(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $class = $fileDefinition->getClass();

        $namespace = $class->getNamespace();
        $namespace->addUse(\App\VO\Context::class);

        $class->addMethod('preConditionsChecks')
            ->setPublic()
            ->setReturnType('void')
            ->addParameter('object');

        $class->getMethod('preConditionsChecks')
            ->addParameter('context')
            ->setType(\App\VO\Context::class);

        $class->getMethod('preConditionsChecks')
            ->addComment('Use assertions, or remove method and interface from the class if not needed.')
            ->addComment('@throws \Exception');

        return $fileDefinition;
    }

    public static function buildInterfaceTagCommandService(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $class = $fileDefinition->getClass();

        $namespace = $class->getNamespace();
        $namespace->addUse(\App\VO\Context::class);

        $class->addMethod('execute')
            ->setPublic()
            ->setReturnType('void')
            ->addParameter('object');

        $class->getMethod('execute')
            ->addParameter('context')
            ->setType(\App\VO\Context::class);

        $class->getMethod('execute')
            ->addComment('@throws \Exception');

        return $fileDefinition;
    }

    public static function buildInterfaceTagQueryService(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $class = $fileDefinition->getClass();

        $namespace = $class->getNamespace();
        $namespace->addUse(\App\VO\Context::class);

        $class->addMethod('fetch')
            ->setPublic()
            ->addParameter('object');

        $class->getMethod('fetch')
            ->addParameter('context')
            ->setType(\App\VO\Context::class);

        $class->getMethod('fetch')
            ->addComment('@throws \Exception');

        return $fileDefinition;
    }

    public static function buildInterfaceCommandService(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $class = $fileDefinition->getClass();

        $namespace = $class->getNamespace();
        $namespace->addUse(\App\VO\Context::class);

        $class->addMethod('execute')
            ->setPublic()
            ->setReturnType('void')
            ->addParameter('object');

        $class->getMethod('execute')
            ->addParameter('context')
            ->setType(\App\VO\Context::class);

        $class->getMethod('execute')
            ->addParameter('service')
            ->setType('?string')
            ->setDefaultValue(null);
        return $fileDefinition;
    }

    public static function buildInterfaceQueryService(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $class = $fileDefinition->getClass();

        $namespace = $class->getNamespace();
        $namespace->addUse(\App\VO\Context::class);

        $class->addMethod('fetch')
            ->setPublic()
            ->addParameter('object');

        $class->getMethod('fetch')
            ->addParameter('context')
            ->setType(\App\VO\Context::class);

        $class->getMethod('fetch')
            ->addParameter('service')
            ->setType('?string')
            ->setDefaultValue(null);

        return $fileDefinition;
    }

    public static function buildHelperAttribute(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Helper', $config);

        $class = $fileDefinition->file->addClass($fileDefinition->fullName());

        $class->addMethod('getNamedArguments')
            ->setStatic()
            ->setPublic()
            ->setReturnType('array')
            ->addParameter('objectOrClass');

        $class->getMethod('getNamedArguments')
            ->addParameter('attributeName')
            ->setType('string');

        $class->getMethod('getNamedArguments')
            ->setBody(<<<'PHP'
$reflectionClass = new \ReflectionClass(get_class($objectOrClass));
$attributes = $reflectionClass->getAttributes();

$attributes = array_filter($attributes, function ($attribute) use($attributeName) {
    return $attribute->getName() === $attributeName;
});

$namedArguments = [];
foreach ($attributes as $attribute) {
    $arguments = $attribute->getArguments();

    $reflectionAttribute = $attribute->newInstance();
    $reflectionMethod = new \ReflectionMethod($reflectionAttribute, '__construct');
    $parameters = $reflectionMethod->getParameters();

    foreach ($parameters as $parameter) {
        $paramName = $parameter->getName();
        $paramPosition = $parameter->getPosition();

        $namedArguments[$paramName] = $arguments[$paramName] ?? $arguments[$paramPosition];
    }
}

return $namedArguments;
PHP);

        $class->addMethod('getParameter')
            ->setStatic()
            ->setPublic()
            ->setReturnType('?string')
            ->addParameter('objectOrClass');

        $class->getMethod('getParameter')
            ->addParameter('attributeName')
            ->setType('string');

        $class->getMethod('getParameter')
            ->addParameter('paramName')
            ->setType('string');

        $class->getMethod('getParameter')
            ->setBody(<<<'PHP'
$params = self::getNamedArguments($objectOrClass, $attributeName);
return $params[$paramName] ?? null;
PHP);

        return $fileDefinition;
    }

    public static function buildServiceCommand(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $class = $fileDefinition->file->addClass($fileDefinition->fullName());
        $class->addImplement(CommandServiceInterface::class);
        $class->setFinal()->setReadOnly();

        $namespace = $class->getNamespace();
        $namespace->addUse(CommandServiceInterface::class);
        $namespace->addUse(FailFastInterface::class);
        $namespace->addUse(PostConditionsChecksInterface::class);
        $namespace->addUse(PreConditionsChecksInterface::class);
        $namespace->addUse(TagCommandServiceInterface::class);
        $namespace->addUse(AttributeHelper::class);
        $namespace->addUse(LoggerInterface::class);
        $namespace->addUse(TaggedIterator::class);
        $namespace->addUse(Assert::class);
        $namespace->addUse(CommandService::class, 'AttributeCommandService');
        $namespace->addUse(\App\VO\Context::class);

        $class->addMethod('__construct')
            ->addPromotedParameter('services')
            ->setPrivate()
            ->setType('iterable')
            ->setDefaultValue([])
            ->addAttribute(TaggedIterator::class, [
                new Literal('TagCommandServiceInterface::class')
            ]);

        $class->getMethod('__construct')
            ->addPromotedParameter('logger')
            ->setPrivate()
            ->setType(LoggerInterface::class);

        $class->addMethod('execute')
            ->setReturnType('void')
            ->addParameter('object');

        $class->getMethod('execute')
            ->addParameter('context')
            ->setType(\App\VO\Context::class);

        $class->getMethod('execute')
            ->addParameter('service')
            ->setType('?string')
            ->setDefaultValue(null);

        $class->getMethod('execute')
            ->addComment('@throws \Exception')
            ->setBody(<<<'PHP'
if (!$this->supports($object, $service)) {
    return;
}

$service ??= $this->getServiceName($object);

Assert::keyExists($this->services, $service, sprintf('Service %s not found', $service));

$this->doExecute($service, $object);
PHP);

        $class->addMethod('doExecute')
            ->setPrivate()
            ->setReturnType('void')
            ->addParameter('service')
            ->setType('string');

        $class->getMethod('doExecute')
            ->addParameter('object');

        $class->getMethod('doExecute')
            ->setBody(<<<'PHP'
$serviceClass = $this->services[$service];
Assert::methodExists($serviceClass, 'execute');

$serviceReflection = new \ReflectionClass($service);

$this->logger->start();
try {
    if ($serviceReflection->implementsInterface(PreConditionsChecksInterface::class)) {
        $serviceClass->preConditionsChecks($object);
    }
    if ($serviceReflection->implementsInterface(FailFastInterface::class)) {
        $serviceClass->failFast($object);
    }

    $serviceClass->execute($object);

    if ($serviceReflection->implementsInterface(PostConditionsChecksInterface::class)) {
        $serviceClass->postConditionsChecks($object);
    }

    $this->logger->success();
    $this->logger->end();
} catch (\Exception $e) {
    $this->logger->exception($e);
    $this->logger->end();
    throw $e;
}
PHP);

        $class->addMethod('supports')
            ->setPrivate()
            ->setReturnType('bool')
            ->addParameter('object');

        $class->getMethod('supports')
            ->addParameter('service')
            ->setType('?string')
            ->setDefaultValue(null);

        $class->getMethod('supports')
            ->setBody(<<<'PHP'
$service ??= $this->getServiceName($object);

if (empty($service)) {
    return false;
}

if (!class_exists($service)) {
    return false;
}

return (new \ReflectionClass($service))
    ->implementsInterface(TagCommandServiceInterface::class);
PHP);

        $class->addMethod('getServiceName')
            ->setPrivate()
            ->setReturnType('string')
            ->addParameter('object');

        $class->getMethod('getServiceName')
            ->setBody('return AttributeHelper::getParameter($object, AttributeCommandService::class, \'serviceName\');');

        return $fileDefinition;
    }

    public static function buildServiceQuery(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $class = $fileDefinition->file->addClass($fileDefinition->fullName());
        $class->addImplement(QueryServiceInterface::class);
        $class->setFinal()->setReadOnly();

        $namespace = $class->getNamespace();
        $namespace->addUse(QueryServiceInterface::class);
        $namespace->addUse(FailFastInterface::class);
        $namespace->addUse(PostConditionsChecksInterface::class);
        $namespace->addUse(PreConditionsChecksInterface::class);
        $namespace->addUse(TagQueryServiceInterface::class);
        $namespace->addUse(AttributeHelper::class);
        $namespace->addUse(LoggerInterface::class);
        $namespace->addUse(TaggedIterator::class);
        $namespace->addUse(Assert::class);
        $namespace->addUse(QueryService::class, 'AttributeQueryService');
        $namespace->addUse(\App\VO\Context::class);

        $class->addMethod('__construct')
            ->addPromotedParameter('services')
            ->setPrivate()
            ->setType('iterable')
            ->setDefaultValue([])
            ->addAttribute(TaggedIterator::class, [
                new Literal('TagQueryServiceInterface::class')
            ]);

        $class->getMethod('__construct')
            ->addPromotedParameter('logger')
            ->setPrivate()
            ->setType(LoggerInterface::class);

        $class->addMethod('fetch')
            ->addParameter('object');

        $class->getMethod('fetch')
            ->addParameter('context')
            ->setType(\App\VO\Context::class);

        $class->getMethod('fetch')
            ->addParameter('service')
            ->setType('?string')
            ->setDefaultValue(null);

        $class->getMethod('fetch')
            ->addComment('@throws \Exception');

        $class->getMethod('fetch')
            ->setBody(<<<'PHP'
if (!$this->supports($object, $service)) {
    return;
}

$service ??= $this->getServiceName($object);

Assert::keyExists($this->services, $service, sprintf('Service %s not found', $service));

$serviceClass = $this->services[$service];
Assert::methodExists($serviceClass, '__invoke');

return $this->doQuery($service, $object);
PHP);

        $class->addMethod('doQuery')
            ->setPrivate()
            ->addParameter('service')
            ->setType('string');

        $class->getMethod('doQuery')
            ->addParameter('object');

        $class->getMethod('doQuery')
            ->addComment('@throws \Exception');

        $class->getMethod('doQuery')
            ->setBody(<<<'PHP'
$serviceClass = $this->services[$service];
Assert::methodExists($serviceClass, 'fetch');

$serviceReflection = new \ReflectionClass($service);

$this->logger->start();
try {
    if ($serviceReflection->implementsInterface(PreConditionsChecksInterface::class)) {
        $serviceClass->preConditionsChecks($object);
    }
    if ($serviceReflection->implementsInterface(FailFastInterface::class)) {
        $serviceClass->failFast($object);
    }

    $result = $serviceClass->fetch($object);

    if ($serviceReflection->implementsInterface(PostConditionsChecksInterface::class)) {
        $serviceClass->postConditionsChecks($object);
    }

    $this->logger->success();
    $this->logger->end();

    return $result;
} catch (\Exception $e) {
    $this->logger->exception($e);
    $this->logger->end();
    throw $e;
}
PHP);

        $class->addMethod('supports')
            ->setPrivate()
            ->setReturnType('bool')
            ->addParameter('object');

        $class->getMethod('supports')
            ->addParameter('service')
            ->setType('?string')
            ->setDefaultValue(null);

        $class->getMethod('supports')
            ->setBody(<<<'PHP'
$service ??= $this->getServiceName($object);

if (empty($service)) {
    return false;
}

if (!class_exists($service)) {
    return false;
}

return (new \ReflectionClass($service))
    ->implementsInterface(TagQueryServiceInterface::class);
PHP);

        $class->addMethod('getServiceName')
            ->setPrivate()
            ->setReturnType('string')
            ->addParameter('object');

        $class->getMethod('getServiceName')
            ->setBody('return AttributeHelper::getParameter($object, AttributeQueryService::class, \'serviceName\');');

        return $fileDefinition;
    }

}
