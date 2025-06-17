<?php

namespace Atournayre\Bundle\MakerBundle\Tests\Unit\Generator;

use Atournayre\Bundle\MakerBundle\Generator\ClassMaker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

class ClassMakerTest extends TestCase
{
    private $twig;
    private $maker;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->maker = new ClassMaker($this->twig, 'TestApp', 'test-src');
    }

    public function testGetCommandName(): void
    {
        $this->assertEquals('make:atournayre:class', $this->maker->getCommandName());
    }

    public function testGetCommandDescription(): void
    {
        $this->assertNotEmpty($this->maker->getCommandDescription());
    }

    public function testConfigureCommand(): void
    {
        $command = $this->createMock(Command::class);
        
        $command->expects($this->once())
            ->method('addArgument')
            ->with(
                'name',
                InputArgument::REQUIRED,
                $this->isType('string')
            )
            ->willReturnSelf();
            
        $command->expects($this->exactly(4))
            ->method('addOption')
            ->withConsecutive(
                [
                    'namespace',
                    null,
                    InputOption::VALUE_OPTIONAL,
                    $this->isType('string'),
                    ''
                ],
                [
                    'extends',
                    null,
                    InputOption::VALUE_OPTIONAL,
                    $this->isType('string')
                ],
                [
                    'implements',
                    null,
                    $this->isType('int'),
                    $this->isType('string'),
                    []
                ],
                [
                    'description',
                    null,
                    InputOption::VALUE_OPTIONAL,
                    $this->isType('string')
                ]
            )
            ->willReturnSelf();
            
        $this->maker->configureCommand($command);
    }

    public function testDoGenerate(): void
    {
        $input = $this->createMock(InputInterface::class);
        $io = $this->createMock(SymfonyStyle::class);
        
        $input->expects($this->once())
            ->method('getArgument')
            ->with('name')
            ->willReturn('TestClass');
            
        $input->expects($this->exactly(4))
            ->method('getOption')
            ->withConsecutive(
                ['namespace'],
                ['extends'],
                ['implements'],
                ['description']
            )
            ->willReturnOnConsecutiveCalls(
                'Domain\\Model',
                'BaseClass',
                ['Interface1', 'Interface2'],
                'Test description'
            );
            
        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                'class/Class.twig',
                $this->callback(function ($params) {
                    return $params['namespace'] === 'TestApp\\Domain\\Model' &&
                           $params['class_name'] === 'TestClass' &&
                           $params['extends'] === 'BaseClass' &&
                           $params['implements'] === ['Interface1', 'Interface2'] &&
                           $params['description'] === 'Test description';
                })
            )
            ->willReturn('rendered class content');
            
        $io->expects($this->once())
            ->method('text')
            ->with($this->isType('array'));
            
        // Use reflection to call the protected method
        $method = new \ReflectionMethod(ClassMaker::class, 'doGenerate');
        $method->setAccessible(true);
        $method->invoke($this->maker, $input, $io);
    }
}
