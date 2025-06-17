<?php

namespace Atournayre\Bundle\MakerBundle\Tests\Integration;

use Atournayre\Bundle\MakerBundle\AtournayeMakerBundle;
use Atournayre\Bundle\MakerBundle\Command\MakerCommand;
use Atournayre\Bundle\MakerBundle\Generator\ClassMaker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class AtournayeMakerBundleTest extends TestCase
{
    public function testBundleRegistration(): void
    {
        $bundle = new AtournayeMakerBundle();
        $this->assertInstanceOf(AtournayeMakerBundle::class, $bundle);
    }

    public function testBundleBuild(): void
    {
        $bundle = new AtournayeMakerBundle();
        $container = new ContainerBuilder();
        
        // This should not throw an exception
        $bundle->build($container);
        
        // Check that the compiler pass was added
        $passes = $container->getCompilerPassConfig()->getPasses();
        $passFound = false;
        foreach ($passes as $pass) {
            if (strpos(get_class($pass), 'MakerCompilerPass') !== false) {
                $passFound = true;
                break;
            }
        }
        
        $this->assertTrue($passFound, 'MakerCompilerPass should be registered');
    }

    public function testBundleGetPath(): void
    {
        $bundle = new AtournayeMakerBundle();
        $path = $bundle->getPath();
        
        $this->assertDirectoryExists($path);
        $this->assertStringEndsWith('maker-bundle', $path);
    }

    /**
     * This is a more complex integration test that would normally be run in a real Symfony application.
     * For simplicity, we're just testing that the services can be created and configured.
     */
    public function testServiceConfiguration(): void
    {
        $container = new ContainerBuilder();
        
        // Register the bundle
        $bundle = new AtournayeMakerBundle();
        $bundle->build($container);
        
        // Register the ClassMaker service
        $container->register('atournayre.maker.class_maker', ClassMaker::class)
            ->setPublic(true)
            ->setArguments([
                $this->createMock(\Twig\Environment::class),
                'App',
                'src'
            ])
            ->addTag('atournayre.maker');
            
        // Compile the container
        $container->compile();
        
        // Check that the service was registered
        $this->assertTrue($container->has('atournayre.maker.class_maker'));
        $this->assertInstanceOf(ClassMaker::class, $container->get('atournayre.maker.class_maker'));
    }
}
