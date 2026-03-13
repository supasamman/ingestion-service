<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\StringableForToStringRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use SavinMikhail\AddNamedArgumentsRector\AddNamedArgumentsRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/bin/console',
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withParallel()
    ->withCache(__DIR__ . '/var/rector')
    ->withPhpSets(php82: true)
    ->withRules([
        AddNamedArgumentsRector::class,
    ])
    ->withSkip([
        StringableForToStringRector::class,
        AddOverrideAttributeToOverriddenMethodsRector::class,
    ]);
