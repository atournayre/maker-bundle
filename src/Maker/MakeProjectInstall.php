<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\VO\Builder\FromTemplateBuilder;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('maker.command')]
class MakeProjectInstall extends AbstractMaker
{
    private bool $enableApiPlatform = false;

    public static function getCommandName(): string
    {
        return 'project:getting-started';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription(self::getCommandDescription())
            ->addOption('enable-api-platform', null, InputOption::VALUE_OPTIONAL, 'Enable ApiPlatform', false);
    }

    public static function getCommandDescription(): string
    {
        return 'Create multiple files to get started with a new project';
    }

    protected function configurations(string $namespace): array
    {
        $templates = $this->getTemplates();
        $configurations = [];
        foreach ($templates as $template) {
            $configurations[] = (new MakerConfig(
                namespace: $namespace,
                builder: FromTemplateBuilder::class,
                enableApiPlatform: $this->enableApiPlatform,
            ))->withTemplatePath($template);
        }
        return $configurations;
    }

    private function getTemplates(): array
    {
        return [
            'Attribute/CommandService.php',
            'Attribute/QueryService.php',
            'ArgumentValueResolver/ContextArgumentValueResolver.php',
            'Exception/FailFast.php',
            'Factory/ContextFactory.php',
            'Helper/AttributeHelper.php',
            'Contracts/Logger/LoggerInterface.php',
            'Contracts/Response/ResponseInterface.php',
            'Contracts/Routing/RoutingInterface.php',
            'Contracts/Security/SecurityInterface.php',
            'Contracts/Security/UserInterface.php',
            'Contracts/Service/CommandServiceInterface.php',
            'Contracts/Service/FailFastInterface.php',
            'Contracts/Service/PostConditionsChecksInterface.php',
            'Contracts/Service/PreConditionsChecksInterface.php',
            'Contracts/Service/QueryServiceInterface.php',
            'Contracts/Service/TagCommandServiceInterface.php',
            'Contracts/Service/TagQueryServiceInterface.php',
            'Contracts/Session/FlashBagInterface.php',
            'Contracts/Templating/TemplatingInterface.php',
            'Contracts/Type/Primitive/ScalarObjectInterface.php',
            'Logger/AbstractLogger.php',
            'Logger/DefaultLogger.php',
            'Logger/NullLogger.php',
            'Service/Response/SymfonyResponseService.php',
            'Service/Routing/SymfonyRoutingService.php',
            'Service/Security/SymfonySecurityService.php',
            'Service/Session/SymfonyFlashBagService.php',
            'Service/Templating/TwigTemplatingService.php',
            'Service/CommandService.php',
            'Service/QueryService.php',
            'Trait/EntityIsTrait.php',
            'Trait/IdEntityTrait.php',
            'Trait/IsTrait.php',
            'Type/Primitive/AbstractCollectionType.php',
            'Type/Primitive/BooleanType.php',
            'Type/Primitive/IntegerType.php',
            'Type/Primitive/ListImmutableType.php',
            'Type/Primitive/ListType.php',
            'Type/Primitive/MapImmutableType.php',
            'Type/Primitive/MapType.php',
            'Type/Primitive/StringType.php',
            'VO/Context.php',
            'VO/DateTime.php',
            'VO/Null/NullUser.php',
        ];
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $deps = [
            \Doctrine\Common\Collections\ArrayCollection::class => 'doctrine/collections',
            \Doctrine\ORM\Mapping\Id::class => 'doctrine/orm',
            \Psr\Clock\ClockInterface::class => 'psr/clock',
            \Psr\Log\LoggerInterface::class => 'psr/log',
            \Symfony\Bundle\SecurityBundle\Security::class => 'symfony/security-bundle',
            \Symfony\Component\DependencyInjection\Attribute\Autowire::class => 'symfony/dependency-injection',
            \Symfony\Component\DependencyInjection\Attribute\TaggedIterator::class => 'symfony/dependency-injection',
            \Symfony\Component\HttpFoundation\BinaryFileResponse::class => 'symfony/http-foundation',
            \Symfony\Component\HttpFoundation\JsonResponse::class => 'symfony/http-foundation',
            \Symfony\Component\HttpFoundation\RedirectResponse::class => 'symfony/http-foundation',
            \Symfony\Component\HttpFoundation\Request::class => 'symfony/http-foundation',
            \Symfony\Component\HttpFoundation\Response::class => 'symfony/http-foundation',
            \Symfony\Component\HttpKernel\Controller\ValueResolverInterface::class => 'symfony/http-kernel',
            \Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata::class => 'symfony/http-kernel',
            \Symfony\Component\Routing\RouterInterface::class => 'symfony/routing',
            \Symfony\Component\String\UnicodeString::class => 'symfony/string',
            \Twig\Environment::class => 'twig/twig',
            \Webmozart\Assert\Assert::class => 'webmozart/assert',
        ];

        if ($this->enableApiPlatform) {
            $deps[\ApiPlatform\Metadata\ApiProperty::class] = 'api-platform/core';
        }

        foreach ($deps as $class => $package) {
            $dependencies->addClassDependency($class, $package);
        }
    }
}
