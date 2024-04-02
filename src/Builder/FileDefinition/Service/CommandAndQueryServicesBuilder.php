<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service;

use App\Attribute\CommandService;
use App\Attribute\CommandService as AttributeCommandService;
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
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
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

        $method = new Method('__construct');
        $method->addParameter('serviceName')->setType('string');
        $construct = $method;

        $property = new Property('serviceName');
        $property->setPublic()->setType('string')->setReadOnly();

        $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->addAttribute('Attribute', [
                new Literal('\Attribute::TARGET_CLASS'),
            ])
            ->addMember($property)
            ->addMember($construct)
        ;

        return $fileDefinition;
    }

    public static function buildAttributeQueryService(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $construct = new Method('__construct');
        $construct->addParameter('serviceName')->setType('string');

        $property = new Property('serviceName');
        $property->setPublic()->setType('string')->setReadOnly();

        $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->addAttribute('Attribute', [
                new Literal('\Attribute::TARGET_CLASS'),
            ])
            ->addMember($property)
            ->addMember($construct)
        ;

        return $fileDefinition;
    }

    public static function buildInterfaceFailFast(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $method = new Method('failFast');
        $method->setPublic()->setReturnType('void');
        $method->addParameter('object');
        $method->addParameter('context')->setType(\App\VO\Context::class);
        $method->addComment('Implement logic here, or remove method and interface from the class if not needed.');
        $method->addComment('@throws FailFast');

        $class = $fileDefinition->getClass()
            ->addMember($method)
        ;

        $class->getNamespace()
            ->addUse(\App\VO\Context::class)
            ->addUse(FailFast::class)
        ;

        return $fileDefinition;
    }

    public static function buildInterfacePostConditionsChecks(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $method = new Method('postConditionsChecks');
        $method->setPublic()->setReturnType('void')->addParameter('object');
        $method->addParameter('context')->setType(\App\VO\Context::class);
        $method
            ->addComment('Use assertions, or remove method and interface from the class if not needed.')
            ->addComment('@throws \Exception');

        $class = $fileDefinition->getClass()
            ->addMember($method)
        ;

        $class->getNamespace()
            ->addUse(\App\VO\Context::class)
        ;

        return $fileDefinition;
    }

    public static function buildInterfacePreConditionsChecks(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $method = new Method('preConditionsChecks');
        $method->setPublic()->setReturnType('void')->addParameter('object');
        $method->addParameter('context')->setType(\App\VO\Context::class);

        $method
            ->addComment('Use assertions, or remove method and interface from the class if not needed.')
            ->addComment('@throws \Exception');

        $class = $fileDefinition->getClass()
            ->addMember($method)
        ;

        $class->getNamespace()
            ->addUse(\App\VO\Context::class)
        ;

        return $fileDefinition;
    }

    public static function buildInterfaceTagCommandService(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $class = $fileDefinition->getClass()
            ->addMember(self::executeInterfaceTagCommandService())
        ;

        $class->getNamespace()
            ->addUse(\App\VO\Context::class)
        ;

        return $fileDefinition;
    }

    public static function executeInterfaceTagCommandService(): Method
    {
        $method = new Method('execute');
        $method->setPublic()->setReturnType('void');
        $method->addParameter('object');
        $method->addParameter('context')->setType(\App\VO\Context::class);
        $method->addComment('@throws \Exception');
        return $method;
    }

    public static function buildInterfaceTagQueryService(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $class = $fileDefinition->getClass()
            ->addMember(self::fetchInterfaceCommandService())
        ;

        $class->getNamespace()
            ->addUse(\App\VO\Context::class)
        ;

        return $fileDefinition;
    }

    public static function fetchInterfaceCommandService(): Method
    {
        $method = new Method('fetch');
        $method->setPublic();
        $method->addParameter('object');
        $method->addParameter('context')->setType(\App\VO\Context::class);
        $method->addComment('@throws \Exception');
        return $method;
    }

    public static function buildInterfaceCommandService(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $method = new Method('execute');
        $method->setPublic()->setReturnType('void');
        $method->addParameter('object');
        $method->addParameter('context')->setType(\App\VO\Context::class);
        $method->addParameter('service')->setType('?string')->setDefaultValue(null);

        $class = $fileDefinition->getClass();
        $class->addMember($method);

        $class->getNamespace()
            ->addUse(\App\VO\Context::class)
        ;
        return $fileDefinition;
    }

    public static function buildInterfaceQueryService(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = InterfaceBuilder::build($config, $namespace, $name);

        $method = new Method('fetch');
        $method->setPublic();
        $method->addParameter('object');
        $method->addParameter('context')->setType(\App\VO\Context::class);
        $method->addParameter('service')->setType('?string')->setDefaultValue(null);

        $class = $fileDefinition->getClass();
        $class->addMember($method);

        $class->getNamespace()
            ->addUse(\App\VO\Context::class)
        ;

        return $fileDefinition;
    }

    public static function buildHelperAttribute(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Helper', $config);

        $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->addMember(self::helperAttributeGetNamedArguments())
            ->addMember(self::helperAttributeGetParameter())
        ;

        return $fileDefinition;
    }

    public static function helperAttributeGetNamedArguments(): Method
    {
        $body = <<<'PHP'
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
PHP;

            $method = new Method('getNamedArguments');
            $method->setStatic();
            $method->setPublic();
            $method->setReturnType('array');
            $method->addParameter('objectOrClass');
            $method->addParameter('attributeName')->setType('string');
            $method->setBody($body);
            return $method;
    }

    public static function helperAttributeGetParameter(): Method
    {
        $body = <<<'PHP'
$params = self::getNamedArguments($objectOrClass, $attributeName);
return $params[$paramName] ?? null;
PHP;

        $method = new Method('getParameter');
        $method->setStatic();
        $method->setPublic();
        $method->setReturnType('?string');
        $method->addParameter('objectOrClass');
        $method->addParameter('attributeName')->setType('string');
        $method->addParameter('paramName')->setType('string');
        $method->setBody($body);
        return $method;
    }

    public static function buildServiceCommand(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->addImplement(CommandServiceInterface::class)
            ->setFinal()
            ->setReadOnly()
            ->addMember(self::constructCommand())
            ->addMember(self::execute())
            ->addMember(self::doExecute())
            ->addMember(self::supportsCommand())
            ->addMember(self::getServiceNameCommand())
            ->addMember(self::getServicesCommand())
        ;

        $class->getNamespace()
            ->addUse(CommandServiceInterface::class)
            ->addUse(FailFastInterface::class)
            ->addUse(PostConditionsChecksInterface::class)
            ->addUse(PreConditionsChecksInterface::class)
            ->addUse(TagCommandServiceInterface::class)
            ->addUse(AttributeHelper::class)
            ->addUse(LoggerInterface::class)
            ->addUse(TaggedIterator::class)
            ->addUse(Assert::class)
            ->addUse(CommandService::class, 'AttributeCommandService')
            ->addUse(\App\VO\Context::class)
        ;

        return $fileDefinition;
    }

    public static function constructCommand(): Method
    {
        $method = new Method('__construct');
        $method
            ->addPromotedParameter('logger')
            ->setPrivate()
            ->setType(LoggerInterface::class);

        $method
            ->addPromotedParameter('services')
            ->setPrivate()
            ->setType('iterable')
            ->setDefaultValue([])
            ->addAttribute(TaggedIterator::class, [
                new Literal('TagCommandServiceInterface::class')
            ]);
        return $method;
    }

    public static function execute(): Method
    {
        $body = <<<'PHP'
if (!$this->supports($object, $service)) {
    return;
}

$service ??= $this->getServiceName($object);

$services = $this->getServices();

Assert::keyExists($services, $service, sprintf('Service %s not found', $service));

$this->doExecute($service, $object, $context);
PHP;

        $method = new Method('execute');
        $method->setPublic()->setReturnType('void');
        $method->addParameter('object');
        $method->addParameter('context')->setType(\App\VO\Context::class);
        $method->addParameter('service')->setType('?string')->setDefaultValue(null);
        $method->addComment('@throws \Exception');
        $method->setBody($body);
        return $method;
    }

    public static function doExecute(): Method
    {
        $body = <<<'PHP'
$serviceClass = $this->getServices()[$service];
Assert::methodExists($serviceClass, 'execute');

$serviceReflection = new \ReflectionClass($service);

$this->logger->start();
try {
    if ($serviceReflection->implementsInterface(PreConditionsChecksInterface::class)) {
        $serviceClass->preConditionsChecks($object, $context);
    }
    if ($serviceReflection->implementsInterface(FailFastInterface::class)) {
        $serviceClass->failFast($object, $context);
    }

    $serviceClass->execute($object, $context);

    if ($serviceReflection->implementsInterface(PostConditionsChecksInterface::class)) {
        $serviceClass->postConditionsChecks($object, $context);
    }

    $this->logger->success();
    $this->logger->end();
} catch (\Exception $e) {
    $this->logger->exception($e);
    $this->logger->end();
    throw $e;
}
PHP;

        $method = new Method('doExecute');
        $method->setPrivate()->setReturnType('void');
        $method->addParameter('service')->setType('string');
        $method->addParameter('object');
        $method->addParameter('context')->setType(\App\VO\Context::class);
        $method->addComment('@throws \Exception');
        $method->setBody($body);
        return $method;

    }

    public static function supportsCommand(): Method
    {
        $body = <<<'PHP'
$service ??= $this->getServiceName($object);

if (empty($service)) {
    return false;
}

if (!class_exists($service)) {
    return false;
}

return (new \ReflectionClass($service))
    ->implementsInterface(TagCommandServiceInterface::class);
PHP;

            $method = new Method('supports');
            $method->setPrivate();
            $method->setReturnType('bool');
            $method->addParameter('object');
            $method->addParameter('service')->setType('?string')->setDefaultValue(null);
            $method->setBody($body);
            return $method;
    }

    public static function getServiceNameCommand(): Method
    {
        $body = <<<'PHP'
$serviceName = AttributeHelper::getParameter($object, AttributeCommandService::class, 'serviceName');

if (empty($serviceName)) {
    throw new \LogicException(sprintf('The Value Object %s requested a CommandService but does not have the attribute CommandService.', get_class($object)));
}

return $serviceName;
PHP;

        $method = new Method('getServiceName');
        $method->setPrivate();
        $method->setReturnType('string');
        $method->addParameter('object');
        $method->setBody($body);
        return $method;
    }

    public static function getServicesCommand(): Method
    {
        $body = <<<'PHP'
$services = [];

if (is_array($this->services)) {
    $services = $this->services;
}

if (is_iterable($this->services)) {
    $services = iterator_to_array($this->services);
}

$servicesNames = array_map(fn($object) => get_class($object), $services);

return array_combine($servicesNames, $services);
PHP;

        $method = new Method('getServices');
        $method->setPrivate();
        $method->setReturnType('array');
        $method->setBody($body);
        return $method;
    }

    public static function buildServiceQuery(
        string $namespace,
        string $name,
        MakerConfig $config
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, '', $config);

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->addImplement(QueryServiceInterface::class)
            ->setFinal()
            ->setReadOnly()
            ->addMember(self::constructQuery())
            ->addMember(self::fetch())
            ->addMember(self::doQuery())
            ->addMember(self::supportsQuery())
            ->addMember(self::getServiceNameQuery())
            ->addMember(self::getServicesQuery())
        ;

        $class->getNamespace()
            ->addUse(QueryServiceInterface::class)
            ->addUse(FailFastInterface::class)
            ->addUse(PostConditionsChecksInterface::class)
            ->addUse(PreConditionsChecksInterface::class)
            ->addUse(TagQueryServiceInterface::class)
            ->addUse(AttributeHelper::class)
            ->addUse(LoggerInterface::class)
            ->addUse(TaggedIterator::class)
            ->addUse(Assert::class)
            ->addUse(QueryService::class, 'AttributeQueryService')
            ->addUse(\App\VO\Context::class)
        ;

        return $fileDefinition;
    }

    private static function constructQuery(): Method
    {
        $method = new Method('__construct');
        $method->addPromotedParameter('logger')->setType(LoggerInterface::class);
        $method->addPromotedParameter('services')->setType('iterable')->setDefaultValue([]);
        $method->addAttribute(TaggedIterator::class, [
            new Literal('TagQueryServiceInterface::class')
        ]);
        return $method;
    }

    private static function fetch(): Method
    {
        $body = <<<'PHP'
if (!$this->supports($object, $service)) {
    return;
}

$service ??= $this->getServiceName($object);

$services = $this->getServices();

Assert::keyExists($services, $service, sprintf('Service %s not found', $service));

$serviceClass = $services[$service];
Assert::methodExists($serviceClass, '__invoke');

return $this->doQuery($service, $object, $context);
PHP;

        $method = new Method('fetch');
        $method->addParameter('object');
        $method->addParameter('context')->setType(\App\VO\Context::class);
        $method->addParameter('service')->setType('?string')->setDefaultValue(null);
        $method->addComment('@throws \Exception');
        $method->setBody($body);
        return $method;
    }

    private static function doQuery(): Method
    {
        $body = <<<'PHP'
$serviceClass = $this->getServices()[$service];
Assert::methodExists($serviceClass, 'fetch');

$serviceReflection = new \ReflectionClass($service);

$this->logger->start();
try {
    if ($serviceReflection->implementsInterface(PreConditionsChecksInterface::class)) {
        $serviceClass->preConditionsChecks($object, $context);
    }
    if ($serviceReflection->implementsInterface(FailFastInterface::class)) {
        $serviceClass->failFast($object, $context);
    }

    $result = $serviceClass->fetch($object, $context);

    if ($serviceReflection->implementsInterface(PostConditionsChecksInterface::class)) {
        $serviceClass->postConditionsChecks($object, $context);
    }

    $this->logger->success();
    $this->logger->end();

    return $result;
} catch (\Exception $e) {
    $this->logger->exception($e);
    $this->logger->end();
    throw $e;
}
PHP;

            $method = new Method('doQuery');
            $method->setPrivate();
            $method->addParameter('service')->setType('string');
            $method->addParameter('object');
            $method->addParameter('context')->setType(\App\VO\Context::class);
            $method->addComment('@throws \Exception');
            $method->setBody($body);
            return $method;

    }

    private static function supportsQuery(): Method
    {
        $body = <<<'PHP'
$service ??= $this->getServiceName($object);

if (empty($service)) {
    return false;
}

if (!class_exists($service)) {
    return false;
}

return (new \ReflectionClass($service))
    ->implementsInterface(TagQueryServiceInterface::class);
PHP;

            $method = new Method('supports');
            $method->setPrivate();
            $method->setReturnType('bool');
            $method->addParameter('object');
            $method->addParameter('service')->setType('?string')->setDefaultValue(null);
            $method->setBody($body);
            return $method;
    }

    private static function getServiceNameQuery(): Method
    {
        $body = <<<'PHP'
$serviceName = AttributeHelper::getParameter($object, AttributeQueryService::class, 'serviceName');

if (empty($serviceName)) {
    throw new \LogicException(sprintf('The Value Object %s requested a QueryService but does not have the attribute QueryService.', get_class($object)));
}

return $serviceName;
PHP;

            $method = new Method('getServiceName');
            $method->setPrivate();
            $method->setReturnType('string');
            $method->addParameter('object');
            $method->setBody($body);
            return $method;
    }

    private static function getServicesQuery(): Method
    {
        $body = <<<'PHP'
$services = [];

if (is_array($this->services)) {
    $services = $this->services;
}

if (is_iterable($this->services)) {
    $services = iterator_to_array($this->services);
}

$servicesNames = array_map(fn($object) => get_class($object), $services);

return array_combine($servicesNames, $services);
PHP;

        $method = new Method('getServices');
        $method->setPrivate();
        $method->setReturnType('array');
        $method->setBody($body);
        return $method;
    }
}
