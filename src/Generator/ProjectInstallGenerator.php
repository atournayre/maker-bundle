<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Generator;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;

class ProjectInstallGenerator extends AbstractGenerator
{
    public function generate(string $namespace, string $name, MakerConfig $config): void
    {
        $config = $this->addRootToConfig($config);

        $this->addFileDefinitionFromTemplate('', 'Attribute/CommandService.php', $config);
        $this->addFileDefinitionFromTemplate('', 'Attribute/QueryService.php', $config);
        $this->addFileDefinitionFromTemplate('ArgumentValueResolver', 'ArgumentValueResolver/ContextArgumentValueResolver.php', $config);
        $this->addFileDefinitionFromTemplate('', 'Exception/FailFast.php', $config);
        $this->addFileDefinitionFromTemplate('Factory', 'Factory/ContextFactory.php', $config);
        $this->addFileDefinitionFromTemplate('Helper', 'Helper/AttributeHelper.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Logger/LoggerInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Response/ResponseInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Routing/RoutingInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Security/SecurityInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Security/UserInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Service/CommandServiceInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Service/FailFastInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Service/PostConditionsChecksInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Service/PreConditionsChecksInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Service/QueryServiceInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Service/TagCommandServiceInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Service/TagQueryServiceInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Templating/TemplatingInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Interface', 'Contracts/Type/Primitive/ScalarObjectInterface.php', $config);
        $this->addFileDefinitionFromTemplate('Logger', 'Logger/AbstractLogger.php', $config);
        $this->addFileDefinitionFromTemplate('Logger', 'Logger/DefaultLogger.php', $config);
        $this->addFileDefinitionFromTemplate('Logger', 'Logger/NullLogger.php', $config);
        $this->addFileDefinitionFromTemplate('Service', 'Service/Response/SymfonyResponseService.php', $config);
        $this->addFileDefinitionFromTemplate('Service', 'Service/Routing/SymfonyRoutingService.php', $config);
        $this->addFileDefinitionFromTemplate('Service', 'Service/Security/SymfonySecurityService.php', $config);
        $this->addFileDefinitionFromTemplate('Service', 'Service/Templating/TwigTemplatingService.php', $config);
        $this->addFileDefinitionFromTemplate('Service', 'Service/CommandService.php', $config);
        $this->addFileDefinitionFromTemplate('Service', 'Service/QueryService.php', $config);
        $this->addFileDefinitionFromTemplate('Trait', 'Trait/EntityIsTrait.php', $config);
        $this->addFileDefinitionFromTemplate('Trait', 'Trait/IdEntityTrait.php', $config);
        $this->addFileDefinitionFromTemplate('Trait', 'Trait/IsTrait.php', $config);
        $this->addFileDefinitionFromTemplate('Type', 'Type/Primitive/AbstractCollectionType.php', $config);
        $this->addFileDefinitionFromTemplate('Type', 'Type/Primitive/BooleanType.php', $config);
        $this->addFileDefinitionFromTemplate('Type', 'Type/Primitive/IntegerType.php', $config);
        $this->addFileDefinitionFromTemplate('Type', 'Type/Primitive/ListImmutableType.php', $config);
        $this->addFileDefinitionFromTemplate('Type', 'Type/Primitive/ListType.php', $config);
        $this->addFileDefinitionFromTemplate('Type', 'Type/Primitive/MapImmutableType.php', $config);
        $this->addFileDefinitionFromTemplate('Type', 'Type/Primitive/MapType.php', $config);
        $this->addFileDefinitionFromTemplate('Type', 'Type/Primitive/StringType.php', $config);
        $this->addFileDefinitionFromTemplate('', 'VO/Context.php', $config);
        $this->addFileDefinitionFromTemplate('', 'VO/DateTime.php', $config);
        $this->addFileDefinitionFromTemplate('', 'VO/Null/NullUser.php', $config);

        $this->generateFiles();
    }
}
