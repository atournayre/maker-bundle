<?php

namespace Atournayre\Bundle\MakerBundle\Tests\Unit\Generator;

use Atournayre\Bundle\MakerBundle\Generator\ClassMaker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

class ClassMakerTest extends TestCase
{
    /**
     * @var Environment&\PHPUnit\Framework\MockObject\MockObject
     */
    private Environment $twig;
    private ClassMaker $maker;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->maker = new ClassMaker($this->twig, 'TestApp', 'test-src');
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ClassMaker::commandName
     */
    public function testCommandName(): void
    {
        self::assertEquals('make:elegant:class', $this->maker->commandName());
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ClassMaker::commandDescription
     */
    public function testCommandDescription(): void
    {
        self::assertNotEmpty($this->maker->commandDescription());
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ClassMaker::configureCommand
     */
    public function testConfigureCommand(): void
    {
        $command = $this->createMock(Command::class);

        $command->expects(self::once())
            ->method('addArgument')
            ->with(
                'name',
                InputArgument::REQUIRED,
                self::isType('string')
            )
            ->willReturnSelf()
        ;

        $command->expects(self::exactly(4))
            ->method('addOption')
            ->willReturnCallback(function ($name, $shortcut, $mode, $description, $default = null) use ($command) {
                static $callCount = 0;
                ++$callCount;

                switch ($callCount) {
                    case 1:
                        self::assertEquals('namespace', $name);
                        self::assertNull($shortcut);
                        self::assertEquals(InputOption::VALUE_OPTIONAL, $mode);
                        self::assertIsString($description);
                        self::assertEquals('', $default);
                        break;
                    case 2:
                        self::assertEquals('extends', $name);
                        self::assertNull($shortcut);
                        self::assertEquals(InputOption::VALUE_OPTIONAL, $mode);
                        self::assertIsString($description);
                        break;
                    case 3:
                        self::assertEquals('implements', $name);
                        self::assertNull($shortcut);
                        self::assertEquals(InputOption::VALUE_OPTIONAL, $mode);
                        self::assertIsString($description);
                        self::assertEquals([], $default);
                        break;
                    case 4:
                        self::assertEquals('description', $name);
                        self::assertNull($shortcut);
                        self::assertEquals(InputOption::VALUE_OPTIONAL, $mode);
                        self::assertIsString($description);
                        break;
                }

                return $command;
            })
            ->willReturnSelf()
        ;

        $this->maker->configureCommand($command);
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ClassMaker::doGenerate
     */
    public function testDoGenerate(): void
    {
        $input = $this->createMock(InputInterface::class);
        $io = $this->createMock(SymfonyStyle::class);

        $input->expects(self::once())
            ->method('getArgument')
            ->with('name')
            ->willReturn('TestClass')
        ;

        $input->expects(self::exactly(5))
            ->method('getOption')
            ->willReturnCallback(function ($option) {
                static $callCount = 0;
                static $namespaceCallCount = 0;
                ++$callCount;

                if ('namespace' === $option) {
                    ++$namespaceCallCount;

                    return 'Domain\\Model';
                } elseif ('extends' === $option) {
                    return 'BaseClass';
                } elseif ('implements' === $option) {
                    return ['Interface1', 'Interface2'];
                } elseif ('description' === $option) {
                    return 'Test description';
                } else {
                    self::fail('Unexpected option: '.$option);
                }
            })
        ;

        $this->twig->expects(self::once())
            ->method('render')
            ->with(
                'class/Class.twig',
                self::callback(function ($params) {
                    return 'TestApp\\Domain\\Model' === $params['namespace']
                           && 'TestClass' === $params['class_name']
                           && 'BaseClass' === $params['extends']
                           && $params['implements'] === ['Interface1', 'Interface2']
                           && 'Test description' === $params['description'];
                })
            )
            ->willReturn('rendered class content')
        ;

        $io->expects(self::once())
            ->method('text')
            ->with(self::isType('array'))
        ;

        // Use reflection to call the protected method
        $method = new \ReflectionMethod(ClassMaker::class, 'doGenerate');
        $method->setAccessible(true);
        $method->invoke($this->maker, $input, $io);
    }
}
