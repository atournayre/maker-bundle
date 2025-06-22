<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\IncreaseDeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/../src',
        __DIR__.'/../tests',
    ])
    ->withSkipPath(__DIR__ . '/../templates')
    ->withPhpSets(
        php82: \true,
    )
    ->withPreparedSets(
        deadCode: \true,
        codeQuality: \true,
        codingStyle: \true,
        privatization: \true,
        earlyReturn: \true,
    )
    ->withImportNames(
        importShortClasses: \false,
        removeUnusedImports: \true,
    )
    ->withRootFiles()
    ->withConfiguredRule(IncreaseDeclareStrictTypesRector::class, [
        'limit' => 10,
    ])
    ;
