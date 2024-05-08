<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withPreparedSets(
        deadCode: true,
//        codeQuality: true,
//        codingStyle: true,
//        typeDeclarations: true,
//        privatization: true,
//        naming: true,
//        earlyReturn: true,
    )
//    ->withPhpSets()
    ->withRootFiles()
//    ->withImportNames(removeUnusedImports: true)
    ;
