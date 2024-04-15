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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use function Symfony\Component\String\u;

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
        $templateDirectory = __DIR__.'/../Resources/templates';

        $filesystem = new Filesystem();
        if (!$filesystem->exists($templateDirectory)) {
            return [];
        }

        $finder = (new Finder())
            ->files()
            ->in($templateDirectory)
            ->name('*.php')
            ->sortByName();

        $templates = [];
        foreach ($finder as $file) {
            $path = u($file->getPathname())->afterLast('Resources/templates/');
            $templates[] = $path->toString();
        }
        return $templates;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $deps = [
            \Atournayre\Collection\TypedCollection::class => 'atournayre/collection',
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
