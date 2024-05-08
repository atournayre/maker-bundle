<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Collection\SplFileInfoCollection;
use Atournayre\Bundle\MakerBundle\Config\FromTemplateMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;
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
            ->setDescription(self::getCommandDescription());
    }

    public static function getCommandDescription(): string
    {
        return 'Create multiple files to get started with a new project';
    }

    /**
     * @throws \Throwable
     */
    protected function configurations(string $namespace): MakerConfigurationCollection
    {
        $configurations = $this->getTemplates()
            ->toMap()
            ->map(function (SplFileInfo $template) {
                $templatePath = u($template->getRealPath())
                    ->afterLast('Resources/templates/')
                    ->prepend($this->rootDir)
                ;

                return FromTemplateMakerConfiguration::fromTemplate(
                    rootDir: $this->rootDir,
                    rootNamespace: $this->rootNamespace,
                    templatePath: $templatePath->toString(),
                )->withSourceCode($template->getContents());
            })
            ->values()
            ->toArray()
        ;
        return MakerConfigurationCollection::createAsList($configurations);
    }

    private function getTemplates(): SplFileInfoCollection
    {
        $templateDirectory = __DIR__.'/../Resources/templates';

        $filesystem = new Filesystem();
        if (!$filesystem->exists($templateDirectory)) {
            return SplFileInfoCollection::createAsMap([]);
        }

        $finder = (new Finder())
            ->files()
            ->in($templateDirectory)
            ->name('*.php')
            ->sortByName();

        $templates = iterator_to_array($finder->getIterator());
        return SplFileInfoCollection::createAsMap($templates);
    }

    /**
     * @return array<string, string>
     */
    protected function dependencies(): array
    {
        $deps = $this->enableApiPlatform
            ? [\ApiPlatform\Metadata\ApiProperty::class => 'api-platform/core']
            : [];

        return [
            ...$deps,
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
    }

    protected function updateConfig(ConsoleStyle $io): void
    {
        $fileServices = $this->rootDir . '/../config/services.yaml';

        $filesystem = new Filesystem();
        if (!$filesystem->exists($fileServices)) {
            $io->error(Str::sprintf('The file %s does not exist', $fileServices));
            return;
        }

        $services = Yaml::parseFile($fileServices);

        $services['services']['App\\']['exclude'][] = '../src/Attribute/';
        $services['services']['App\\']['exclude'][] = '../src/Exception/';
        $appExclude = array_unique($services['services']['App\\']['exclude']);
        $services['services']['App\\']['exclude'] = $appExclude;

        $services['services']['app.logger.toto'] = [
            'class' => 'App\\Logger\\DefaultLogger',
            'calls' => [
                ['setLoggerIdentifier', ['toto']]
            ]
        ];

        $services['services']['App\Contracts\Logger\LoggerInterface'] = '@App\Logger\DefaultLogger';
        $services['services']['App\Contracts\Session\FlashBagInterface'] = '@App\Service\Session\SymfonyFlashBagService';
        $services['services']['App\Contracts\Service\CommandServiceInterface'] = '@App\Service\CommandService';
        $services['services']['App\Contracts\Service\QueryServiceInterface'] = '@App\Service\QueryService';
        $services['services'][\Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface::class] = [
            'class' => \Symfony\Component\HttpFoundation\Session\Flash\FlashBag::class,
            'public' => true
        ];

        $yaml = Yaml::dump($services, 4);
        file_put_contents($fileServices, $yaml);

        $io->success('The file services.yaml has been updated');
    }
}
