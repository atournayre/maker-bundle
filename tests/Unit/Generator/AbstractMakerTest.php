<?php

namespace Atournayre\Bundle\MakerBundle\Tests\Unit\Generator;

use Atournayre\Bundle\MakerBundle\Generator\AbstractMaker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

class AbstractMakerTest extends TestCase
{
    private $twig;
    private $maker;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->maker = $this->getMockForAbstractClass(
            AbstractMaker::class,
            [$this->twig, 'TestApp', 'test-src']
        );
    }

    public function testGetNamespace(): void
    {
        $method = new \ReflectionMethod(AbstractMaker::class, 'getNamespace');
        $method->setAccessible(true);

        $this->assertEquals('TestApp', $method->invoke($this->maker));
        $this->assertEquals('TestApp\\Domain', $method->invoke($this->maker, 'Domain'));
        $this->assertEquals('TestApp\\Domain\\Model', $method->invoke($this->maker, 'Domain\\Model'));
    }

    public function testGetPath(): void
    {
        $method = new \ReflectionMethod(AbstractMaker::class, 'getPath');
        $method->setAccessible(true);

        $this->assertEquals('test-src', $method->invoke($this->maker));
        $this->assertEquals('test-src/Domain', $method->invoke($this->maker, 'Domain'));
        $this->assertEquals('test-src/Domain/Model', $method->invoke($this->maker, 'Domain/Model'));
    }

    public function testGenerateFile(): void
    {
        $this->twig->expects($this->once())
            ->method('render')
            ->with('test-template.twig', ['param' => 'value'])
            ->willReturn('rendered content')
        ;

        // Create a test subclass that exposes the protected method
        $testMaker = new class($this->twig, 'TestApp', 'test-src') extends AbstractMaker {
            public function getCommandName(): string
            {
                return 'test:command';
            }

            public function getCommandDescription(): string
            {
                return 'Test command';
            }

            public function configureCommand(Command $command): void
            {
            }

            protected function doGenerate(InputInterface $input, SymfonyStyle $io): void
            {
            }

            // Expose the protected method for testing
            public function publicGenerateFile(string $targetPath, string $template, array $parameters = []): void
            {
                $this->generateFile($targetPath, $template, $parameters);
            }
        };

        // Use vfsStream or similar to test file creation in a real test
        // For this example, we'll just test that the method doesn't throw an exception
        $this->expectNotToPerformAssertions();

        // We can't actually write to the filesystem in a unit test, so we'll just
        // verify that the method doesn't throw an exception
        $testMaker->publicGenerateFile('test-path', 'test-template.twig', ['param' => 'value']);
    }

    public function testGenerate(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $this->maker = $this->getMockForAbstractClass(
            AbstractMaker::class,
            [$this->twig, 'TestApp', 'test-src'],
            '',
            true,
            true,
            true,
            ['doGenerate']
        );

        $this->maker->expects($this->once())
            ->method('doGenerate')
            ->with($input, $this->isInstanceOf(SymfonyStyle::class))
        ;

        $result = $this->maker->generate($input, $output);

        $this->assertEquals(Command::SUCCESS, $result);
    }

    public function testGenerateWithException(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $this->maker = $this->getMockForAbstractClass(
            AbstractMaker::class,
            [$this->twig, 'TestApp', 'test-src'],
            '',
            true,
            true,
            true,
            ['doGenerate']
        );

        $this->maker->expects($this->once())
            ->method('doGenerate')
            ->with($input, $this->isInstanceOf(SymfonyStyle::class))
            ->willThrowException(new \Exception('Test exception'))
        ;

        $result = $this->maker->generate($input, $output);

        $this->assertEquals(Command::FAILURE, $result);
    }
}
