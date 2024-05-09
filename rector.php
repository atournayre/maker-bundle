<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
//        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
    )
    ->withSkip([
        __DIR__ . '/src/DependencyInjection',
        __DIR__ . 'src/Maker/AbstractMaker.php',
    ])
    ->withPhpSets()
    ->withRootFiles()
    ->withImportNames(
        importShortClasses: false,
        removeUnusedImports: true,
    )
    ;
