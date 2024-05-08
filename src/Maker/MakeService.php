<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Maker;

use App\Attribute\CommandService;
use App\Attribute\QueryService;
use Atournayre\Bundle\MakerBundle\Collection\MakerConfigurationCollection;
use Atournayre\Bundle\MakerBundle\Config\AddAttributeMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Config\ServiceCommandMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Config\ServiceQueryMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Nette\PhpGenerator\Attribute;
use Nette\PhpGenerator\Literal;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

#[AutoconfigureTag('maker.command')]
class MakeService extends AbstractMaker
{
    private const COMMAND = 'Command';
    private const QUERY = 'Query';

    private ?string $vo = null;
    private ?string $serviceType = null;
    private ?string $attributeName = null;

    public static function getCommandName(): string
    {
        return 'make:new:service';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new Service')
            ->addArgument('namespace', InputArgument::REQUIRED, 'The class name of the Service <fg=yellow>(e.g. DummyCommandService, DummyQueryService)</>')
            ->addOption('command', null, InputOption::VALUE_NONE, 'Create a Command Service')
            ->addOption('query', null, InputOption::VALUE_NONE, 'Create a Query Service')
        ;
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Service';
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        parent::interact($input, $io, $command);

        $availableVOs = $this->availableVOs();

        if (empty($availableVOs)) {
            $io->error('No VO available. Please create a VO first. Use "make:new:vo" command.');
            return;
        }

        $isCommand = true === $input->getOption('command');
        $isQuery = true === $input->getOption('query');

        if ($isCommand && $isQuery) {
            throw new \InvalidArgumentException('You can only choose one type of service at a time. Either command or query.');
        }

        $this->serviceType = $isCommand
            ? self::COMMAND
            : ($isQuery ? self::QUERY : null);

        if (null === $this->serviceType) {
            $questionCommandOrQuery = new ChoiceQuestion('Choose the type of service', [
                self::COMMAND,
                self::QUERY,
            ]);
            $this->serviceType = $io->askQuestion($questionCommandOrQuery);
        }

        $io->section(sprintf('Create a new %s Service', $this->serviceType));

        $vo = new ChoiceQuestion('Choose the VO', $availableVOs);
        $voName = $io->askQuestion($vo);

        $this->vo = $voName;

        $this->attributeName = match ($this->serviceType) {
            self::COMMAND => CommandService::class,
            self::QUERY => QueryService::class,
            default => throw new \InvalidArgumentException('Invalid command or query'),
        };
    }

    /**
     * @return array<string>
     */
    private function availableVOs(): array
    {
        return $this->bundleConfiguration->resources->service->allowedTypes($this->filesystem);
    }

    /**
     * @throws \Throwable
     */
    protected function configurations(string $namespace): MakerConfigurationCollection
    {
        $voClassName = Str::classNameFromNamespace(Str::namespaceFromPath($this->vo, $this->rootDir), '');

        $addAttributeMakerConfiguration = AddAttributeMakerConfiguration::fromNamespace(
            rootDir: $this->rootDir,
            rootNamespace: $this->rootNamespace,
            namespace: $this->configNamespaces->vo,
            className: $voClassName,
        );

        $serviceNamespace = $this->serviceType === self::COMMAND
            ? $this->configNamespaces->serviceCommand
            : $this->configNamespaces->serviceQuery;

        $serviceCommandMakerConfiguration = ServiceCommandMakerConfiguration::fromNamespace(
            rootDir: $this->rootDir,
            rootNamespace: $this->rootNamespace,
            namespace: $serviceNamespace,
            className: $namespace,
        )
            ->withVo($addAttributeMakerConfiguration->fqcn);

        $serviceQueryMakerConfiguration = ServiceQueryMakerConfiguration::fromNamespace(
            rootDir: $this->rootDir,
            rootNamespace: $this->rootNamespace,
            namespace: $serviceNamespace,
            className: $namespace,
        )
            ->withVo($addAttributeMakerConfiguration->fqcn);

        $serviceMakerConfiguration = $this->serviceType === self::COMMAND
            ? $serviceCommandMakerConfiguration
            : $serviceQueryMakerConfiguration;

        $addAttributeMakerConfiguration = $addAttributeMakerConfiguration
            ->withServiceNamespace($serviceMakerConfiguration->fqcn)
            ->withAttributes([
                new Attribute($this->attributeName, [
                    'serviceName' => new Literal(Str::classNameSemiColonFromNamespace($serviceMakerConfiguration->fqcn))
                ])
            ])
        ;

        $configurations = [];
        $configurations[] = $addAttributeMakerConfiguration;
        $configurations[] = $serviceMakerConfiguration;

        return MakerConfigurationCollection::createAsList($configurations);
    }

    /**
     * @return array<string, string>
     */
    protected function dependencies(): array
    {
        return [
            \Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag::class => 'symfony/dependency-injection',
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

        $services['services']['App\Contracts\Service\CommandServiceInterface'] = '@App\Service\CommandService';
        $services['services']['App\Contracts\Service\QueryServiceInterface'] = '@App\Service\QueryService';

        $yaml = Yaml::dump($services, 4);
        file_put_contents($fileServices, $yaml);

        $io->success('The file services.yaml has been updated');
    }
}
