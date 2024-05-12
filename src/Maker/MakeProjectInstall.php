<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use \Carbon\Carbon;
use ApiPlatform\Metadata\ApiProperty;
use App\Contracts\Service\CommandServiceInterface;
use App\Contracts\Service\QueryServiceInterface;
use App\Logger\DefaultLogger;
use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Collection\SplFileInfoCollection;
use Atournayre\Bundle\MakerBundle\Config\FromTemplateMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Config\MakerConfiguration;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Collection\TypedCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Id;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Webmozart\Assert\Assert;
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
            ->map(function (SplFileInfo $template): MakerConfiguration {
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
            ? [ApiProperty::class => 'api-platform/core']
            : [];

        return [
            ...$deps,
            TypedCollection::class => 'atournayre/collection',
            ArrayCollection::class => 'doctrine/collections',
            Id::class => 'doctrine/orm',
            ClockInterface::class => 'psr/clock',
            LoggerInterface::class => 'psr/log',
            Security::class => 'symfony/security-bundle',
            Autowire::class => 'symfony/dependency-injection',
            TaggedIterator::class => 'symfony/dependency-injection',
            BinaryFileResponse::class => 'symfony/http-foundation',
            JsonResponse::class => 'symfony/http-foundation',
            RedirectResponse::class => 'symfony/http-foundation',
            Request::class => 'symfony/http-foundation',
            Response::class => 'symfony/http-foundation',
            ValueResolverInterface::class => 'symfony/http-kernel',
            ArgumentMetadata::class => 'symfony/http-kernel',
            RouterInterface::class => 'symfony/routing',
            UnicodeString::class => 'symfony/string',
            Environment::class => 'twig/twig',
            Assert::class => 'webmozart/assert',
            Carbon::class => 'nesbot/carbon',
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
            'class' => DefaultLogger::class,
            'calls' => [
                ['setLoggerIdentifier', ['toto']]
            ]
        ];

        $services['services'][\App\Contracts\Logger\LoggerInterface::class] = '@App\Logger\DefaultLogger';
        $services['services'][\App\Contracts\Session\FlashBagInterface::class] = '@App\Service\Session\SymfonyFlashBagService';
        $services['services'][CommandServiceInterface::class] = '@App\Service\CommandService';
        $services['services'][QueryServiceInterface::class] = '@App\Service\QueryService';
        $services['services'][FlashBagInterface::class] = [
            'class' => FlashBag::class,
            'public' => true
        ];

        $yaml = Yaml::dump($services, 4);
        file_put_contents($fileServices, $yaml);

        $io->success('The file services.yaml has been updated');
    }
}
