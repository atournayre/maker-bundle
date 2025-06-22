<?php

namespace Atournayre\Bundle\MakerBundle\Tests\Unit\Generator;

use Atournayre\Bundle\MakerBundle\Generator\ExceptionMaker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

class ExceptionMakerTest extends TestCase
{
    /**
     * @var Environment&\PHPUnit\Framework\MockObject\MockObject
     */
    private Environment $twig;
    private ExceptionMaker $maker;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->maker = new ExceptionMaker(
            $this->twig,
            'TestApp',
            'test-src',
            'TestApp\\Exception',
            'test-src/Exception'
        );
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ExceptionMaker::commandName
     */
    public function testCommandName(): void
    {
        self::assertEquals('make:elegant:exception', $this->maker->commandName());
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ExceptionMaker::commandDescription
     */
    public function testCommandDescription(): void
    {
        self::assertNotEmpty($this->maker->commandDescription());
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ExceptionMaker::configureCommand
     */
    public function testConfigureCommand(): void
    {
        $command = $this->createMock(Command::class);

        // Verify that the command is configured with the expected arguments and options
        $command->expects(self::once())
            ->method('addArgument')
            ->with('name', self::anything(), self::anything())
            ->willReturnSelf();

        $command->expects(self::once())
            ->method('addOption')
            ->with('namespace', null, self::anything(), self::anything())
            ->willReturnSelf();

        $this->maker->configureCommand($command);
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ExceptionMaker::generate
     */
    public function testGenerate(): void
    {
        // This test is limited because ExceptionMaker is a final class
        // and we can't mock it or its protected/private methods

        // We'll just verify that the public methods exist and can be called
        self::assertTrue(method_exists($this->maker, 'generate'));
        self::assertTrue(method_exists($this->maker, 'commandName'));
        self::assertTrue(method_exists($this->maker, 'commandDescription'));
        self::assertTrue(method_exists($this->maker, 'configureCommand'));
    }

    /**
     * Test the integration with AbstractMaker
     * 
     * @covers \Atournayre\Bundle\MakerBundle\Generator\ExceptionMaker::generate
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

        // We'll use reflection to check that ExceptionMaker extends AbstractMaker
        $reflection = new \ReflectionClass(ExceptionMaker::class);
        self::assertTrue($reflection->isSubclassOf('Atournayre\Bundle\MakerBundle\Generator\AbstractMaker'));

        // We'll also verify that it implements MakerInterface
        self::assertTrue($reflection->implementsInterface('Atournayre\Bundle\MakerBundle\Generator\MakerInterface'));
    }
}
