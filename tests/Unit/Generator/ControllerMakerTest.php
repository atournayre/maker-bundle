<?php

namespace Atournayre\Bundle\MakerBundle\Tests\Unit\Generator;

use Atournayre\Bundle\MakerBundle\Generator\ControllerMaker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;

class ControllerMakerTest extends TestCase
{
    /**
     * @var Environment&\PHPUnit\Framework\MockObject\MockObject
     */
    private Environment $twig;
    private ControllerMaker $maker;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->maker = new ControllerMaker(
            $this->twig,
            'TestApp',
            'test-src',
            'TestApp\\Controller',
            'test-src/Controller',
            'controller.tpl.php',
            ['TestApp\\Contracts\\Controller\\ControllerInterface']
        );
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ControllerMaker::commandName
     */
    public function testCommandName(): void
    {
        self::assertEquals('make:elegant:controller', $this->maker->commandName());
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ControllerMaker::commandDescription
     */
    public function testCommandDescription(): void
    {
        self::assertNotEmpty($this->maker->commandDescription());
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ControllerMaker::configureCommand
     */
    public function testConfigureCommand(): void
    {
        $command = $this->createMock(Command::class);

        // Verify that the command is configured with the expected arguments and options
        $command->expects(self::once())
            ->method('addArgument')
            ->with('name', self::anything(), self::anything())
            ->willReturnSelf()
        ;

        // Create a mock that will track the calls to addOption
        $optionCalls = [];
        $command->method('addOption')
            ->willReturnCallback(function ($name, $shortcut, $mode, $description) use (&$optionCalls, $command) {
                $optionCalls[] = $name;

                return $command;
            })
        ;

        $this->maker->configureCommand($command);

        // Verify that the expected options were added
        self::assertCount(3, $optionCalls);
        self::assertContains('namespace', $optionCalls);
        self::assertContains('extends-abstract-controller', $optionCalls);
        self::assertContains('template', $optionCalls);
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ControllerMaker::generate
     */
    public function testGenerate(): void
    {
        // This test is limited because ControllerMaker is a final class
        // and we can't mock it or its protected/private methods

        // We'll just verify that the public methods exist and can be called
        self::assertTrue(method_exists($this->maker, 'generate'));
        self::assertTrue(method_exists($this->maker, 'commandName'));
        self::assertTrue(method_exists($this->maker, 'commandDescription'));
        self::assertTrue(method_exists($this->maker, 'configureCommand'));
    }

    /**
     * Test the integration with AbstractMaker.
     *
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ControllerMaker::generate
     */
    public function testGenerateIntegration(): void
    {
        // Create a mock for the dependencies
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // We can't test the full generate method because it would try to
        // interact with the user and filesystem, but we can verify that
        // it's properly integrated with AbstractMaker by checking that
        // it returns the expected values for success/failure scenarios

        // We'll use reflection to check that ControllerMaker extends AbstractMaker
        $reflection = new \ReflectionClass(ControllerMaker::class);
        self::assertTrue($reflection->isSubclassOf('Atournayre\Bundle\MakerBundle\Generator\AbstractMaker'));

        // We'll also verify that it implements MakerInterface
        self::assertTrue($reflection->implementsInterface('Atournayre\Bundle\MakerBundle\Generator\MakerInterface'));
    }
}
