<?php

namespace Atournayre\Bundle\MakerBundle\Tests\Integration;

use Atournayre\Bundle\MakerBundle\ElegantMakerBundle;
use Atournayre\Bundle\MakerBundle\Generator\ClassMaker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ElegantMakerBundleTest extends TestCase
{
    /**
     * @covers \Atournayre\Bundle\MakerBundle\ElegantMakerBundle::build
     */
    public function testBundleBuild(): void
    {
        $bundle = new ElegantMakerBundle();
        $container = new ContainerBuilder();

        // This should not throw an exception
        $bundle->build($container);

        // Check that the compiler pass was added
        $passes = $container->getCompilerPassConfig()->getPasses();
        $passFound = false;
        foreach ($passes as $pass) {
            if (false !== strpos(get_class($pass), 'MakerCompilerPass')) {
                $passFound = true;
                break;
            }
        }

        self::assertTrue($passFound, 'MakerCompilerPass should be registered');
    }

    /**
     * @covers \Atournayre\Bundle\MakerBundle\ElegantMakerBundle::getPath
     */
    public function testBundleGetPath(): void
    {
        $bundle = new ElegantMakerBundle();
        $path = $bundle->getPath();

        self::assertDirectoryExists($path);
        self::assertStringEndsWith('maker-bundle', $path);
    }

    /**
     * This is a more complex integration test that would normally be run in a real Symfony application.
     * For simplicity, we're just testing that the services can be created and configured.
     *
     * @covers \Atournayre\Bundle\MakerBundle\ElegantMakerBundle::build
     * @covers \Atournayre\Bundle\MakerBundle\DependencyInjection\CompilerPass\MakerCompilerPass
     */
    public function testServiceConfiguration(): void
    {
        $container = new ContainerBuilder();

        // Register the bundle
        $bundle = new ElegantMakerBundle();
        $bundle->build($container);

        // Register the ClassMaker service
        $container->register('elegant.maker.class_maker', ClassMaker::class)
            ->setPublic(true)
            ->setArguments([
                $this->createMock(\Twig\Environment::class),
                'App',
                'src',
            ])
            ->addTag('elegant.maker')
        ;

        // Compile the container
        $container->compile();

        // Check that the service was registered
        self::assertTrue($container->has('elegant.maker.class_maker'));
        self::assertInstanceOf(ClassMaker::class, $container->get('elegant.maker.class_maker'));
    }
}
