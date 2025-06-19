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
    /**
     * @var Environment&\PHPUnit\Framework\MockObject\MockObject
     */
    private Environment $twig;
    private AbstractMaker $maker;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        // Create a concrete implementation of the abstract class for testing
        $this->maker = new class($this->twig, 'TestApp', 'test-src') extends AbstractMaker {
            public function commandName(): string
            {
                return 'test:command';
            }

            public function commandDescription(): string
            {
                return 'Test command';
            }

            public function configureCommand(Command $command): void
            {
            }

            protected function doGenerate(InputInterface $input, SymfonyStyle $io): void
            {
            }
        };
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\AbstractMaker::namespace
     */
    public function testNamespace(): void
    {
        $method = new \ReflectionMethod(AbstractMaker::class, 'namespace');
        $method->setAccessible(true);

        self::assertEquals('TestApp', $method->invoke($this->maker));
        self::assertEquals('TestApp\\Domain', $method->invoke($this->maker, 'Domain'));
        self::assertEquals('TestApp\\Domain\\Model', $method->invoke($this->maker, 'Domain\\Model'));
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\AbstractMaker::path
     */
    public function testPath(): void
    {
        $method = new \ReflectionMethod(AbstractMaker::class, 'path');
        $method->setAccessible(true);

        self::assertEquals('test-src', $method->invoke($this->maker));
        self::assertEquals('test-src/Domain', $method->invoke($this->maker, 'Domain'));
        self::assertEquals('test-src/Domain/Model', $method->invoke($this->maker, 'Domain/Model'));
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\AbstractMaker::generateFile
     */
    public function testGenerateFile(): void
    {
        $this->twig->expects(self::once())
            ->method('render')
            ->with('test-template.twig', ['param' => 'value'])
            ->willReturn('rendered content')
        ;

        // Create a test subclass that exposes the protected method
        $testMaker = new class($this->twig, 'TestApp', 'test-src') extends AbstractMaker {
            public function commandName(): string
            {
                return 'test:command';
            }

            public function commandDescription(): string
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
            /**
             * @param array<string, mixed> $parameters
             *
             * @api
             */
            public function publicGenerateFile(string $targetPath, string $template, array $parameters = []): void
            {
                $this->generateFile($targetPath, $template, $parameters);
            }
        };

        // We can't actually write to the filesystem in a unit test, so we'll just
        // verify that the method doesn't throw an exception and that the Twig render method is called
        $testMaker->publicGenerateFile('test-path', 'test-template.twig', ['param' => 'value']);
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\AbstractMaker::generate
     */
    public function testGenerate(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // Create a test-specific subclass with a mock doGenerate method
        $testMaker = $this->getMockBuilder(AbstractMaker::class)
            ->setConstructorArgs([$this->twig, 'TestApp', 'test-src'])
            ->onlyMethods(['doGenerate', 'commandName', 'commandDescription', 'configureCommand'])
            ->getMock()
        ;

        $testMaker->expects(self::once())
            ->method('doGenerate')
            ->with($input, self::isInstanceOf(SymfonyStyle::class))
        ;

        $testMaker->method('commandName')->willReturn('test:command');
        $testMaker->method('commandDescription')->willReturn('Test command');

        $result = $testMaker->generate($input, $output);

        self::assertEquals(Command::SUCCESS, $result);
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\AbstractMaker::generate
     */
    public function testGenerateWithException(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // Create a test-specific subclass with a mock doGenerate method that throws an exception
        $testMaker = $this->getMockBuilder(AbstractMaker::class)
            ->setConstructorArgs([$this->twig, 'TestApp', 'test-src'])
            ->onlyMethods(['doGenerate', 'commandName', 'commandDescription', 'configureCommand'])
            ->getMock()
        ;

        $testMaker->expects(self::once())
            ->method('doGenerate')
            ->with($input, self::isInstanceOf(SymfonyStyle::class))
            ->willThrowException(new \Exception('Test exception'))
        ;

        $testMaker->method('commandName')->willReturn('test:command');
        $testMaker->method('commandDescription')->willReturn('Test command');

        $result = $testMaker->generate($input, $output);

        self::assertEquals(Command::FAILURE, $result);
    }
}
