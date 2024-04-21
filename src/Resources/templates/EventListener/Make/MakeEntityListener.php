<?php
declare(strict_types=1);

namespace App\EventListener\Make;

use App\Collection\EventCollection;
use App\Contracts\Event\HasEventsInterface;
use App\Entity\Traits\BlameableEntityTrait;
use App\Entity\Traits\TimestampableEntityTrait;
use App\Trait\EventsTrait;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use App\Trait\Entity\IdTrait;
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
        if (!$this->supports($event)) {
            return;
        }

        $this->decorateEntity($event);
    }

    private function supports(ConsoleTerminateEvent $event): bool
    {
        return $event->getCommand()->getName() === 'make:entity';
    }

    private function decorateEntity(ConsoleTerminateEvent $event): void
    {
        $entityName = $event->getInput()->getArgument('name');

        $entity = $this->getEntityFileByName($entityName);

        $phpFile = PhpFile::fromCode($entity->getContents());

        $this->updateId($phpFile);
        $this->addEvents($phpFile);
        $this->addTrait($phpFile, BlameableEntityTrait::class);
        $this->addTrait($phpFile, TimestampableEntityTrait::class);
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
        $namespace->addUse(HasEventsInterface::class);

        $implements = $class->getImplements();
        $implements[] = HasEventsInterface::class;
        $class->setImplements(array_unique($implements));

        $traits = $class->getTraits();

        if (array_key_exists(EventsTrait::class, $traits)) {
            return;
        }

        $class->addTrait(EventsTrait::class);

        $class->hasMethod('__construct') ?: $class->addMethod('__construct');
        $constructor = $class->getMethod('__construct');
        $constructor->addBody('$this->events = EventCollection::createAsList([]);');
    }

    private function addTrait(PhpFile $phpFile, string $trait): void
    {
        $classes = $phpFile->getClasses();
        $class = array_shift($classes);

        $namespace = $class->getNamespace();
        $namespace->addUse($trait);

        $traits = $class->getTraits();

        if (array_key_exists($trait, $traits)) {
            return;
        }

        $class->addTrait($trait);
    }

    private function updateId(PhpFile $phpFile): void
    {
        $classes = $phpFile->getClasses();
        $class = array_shift($classes);

        if ($class->hasProperty('id')) {
            $class->removeProperty('id');
            $class->removeMethod('getId');
        }

        $class->addTrait(IdTrait::class);
    }
}
