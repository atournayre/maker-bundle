<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Tests\Builder;

use Atournayre\Bundle\MakerBundle\Builder\ControllerBuilder;
use Atournayre\Bundle\MakerBundle\Config\ControllerMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter;
use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureEntity;
use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureFormType;
use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureVo;
use Symfony\Component\Finder\SplFileInfo;

class ControllerBuilderTest extends BuilderTestCase
{
    public static function dataProvider(): array
    {
        $withFormControllerPath = __DIR__.'/../../src/Resources/templates/Controller/WithFormController.php';
        $withFormController = new SplFileInfo($withFormControllerPath, $withFormControllerPath, $withFormControllerPath);

        return [
            'Controller' => [
                'make-controller/DummyController.php',
                'App\Controller',
                'Dummy',
                ControllerMakerConfiguration::fromNamespace(
                    rootDir: __DIR__,
                    rootNamespace: self::rootNamespace(),
                    namespace: 'App\Controller',
                    className: 'Dummy',
                )
                    ->withSourceCode($withFormController->getContents())
                    ->withEntityNamespace(FixtureEntity::class)
                    ->withFormTypeNamespace(FixtureFormType::class)
                    ->withVoNamespace(FixtureVo::class),
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers \Atournayre\Bundle\MakerBundle\Builder\ControllerBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Builder\ControllerBuilder::supports
     * @covers \Atournayre\Bundle\MakerBundle\Builder\AbstractBuilder::createPhpFileDefinition
     * @covers \Atournayre\Bundle\MakerBundle\Config\ControllerMakerConfiguration::fromNamespace
     * @covers \Atournayre\Bundle\MakerBundle\Printer\PhpFilePrinter::print
     */
    public function testBuildControllerOfSourceCode(string $haystackFilePath, string $namespace, string $className, ControllerMakerConfiguration $makerConfiguration): void
    {
        $haystackFilePath = self::fixtureFilePath($haystackFilePath);

        $builder = new ControllerBuilder();

        $phpFileDefinition = $builder->createPhpFileDefinition($makerConfiguration);
        $sourceCode = PhpFilePrinter::create($phpFileDefinition)->print();

        self::assertFileContentEquals($sourceCode, $haystackFilePath);
    }
}
