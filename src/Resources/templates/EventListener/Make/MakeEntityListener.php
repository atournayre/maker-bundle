<?php
declare(strict_types=1);

namespace App\EventListener\Make;

use App\Collection\EventCollection;
use App\Trait\EventsTrait;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Nette\PhpGenerator\PhpFile;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

#[AsEventListener(event: 'console.terminate')]
final class MakeEntityListener
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/src')]
        private readonly string $rootDir,
    )
    {
    }

    public function __invoke(ConsoleTerminateEvent $event): void
    {
        if ($event->getCommand()->getName() !== 'make:entity') {
            return;
        }

        // Add limitations to exit early
        // e.g. Skip a specific entity

        $this->decorateEntity($event);
    }

    private function decorateEntity(ConsoleTerminateEvent $event): void
    {
        $entityName = $event->getInput()->getArgument('name');

        $entity = $this->getEntityFileByName($entityName);

        $phpFile = PhpFile::fromCode($entity->getContents());

        $this->addEvents($phpFile);
        // Add other changes here

        file_put_contents($entity->getPathname(), (string)$phpFile);
    }

    private function getEntityFileByName(string $entityName): SplFileInfo
    {
        $entityDirectory = Str::sprintf('%s/Entity', $this->rootDir);

        $finder = (new Finder())
            ->files()
            ->in($entityDirectory)
            ->name($entityName . '.php');

        if (0 === $finder->count()) {
            throw new \RuntimeException('Entity not found');
        }

        if (1 < $finder->count()) {
            throw new \RuntimeException('Multiple entities found');
        }

        foreach ($finder as $file) {
            return $file;
        }

        throw new \RuntimeException('Entity not found');
    }

    private function addEvents(PhpFile $phpFile): void
    {
        $classes = $phpFile->getClasses();
        $class = array_shift($classes);

        $namespace = $class->getNamespace();
        $namespace->addUse(EventsTrait::class);
        $namespace->addUse(EventCollection::class);

        $traits = $class->getTraits();

        if (array_key_exists(EventsTrait::class, $traits)) {
            return;
        }

        $class->addTrait(EventsTrait::class);

        $class->hasMethod('__construct') ?: $class->addMethod('__construct');
        $constructor = $class->getMethod('__construct');
        $constructor->addBody('$this->events = EventCollection::createAsList([]);');
    }
}
