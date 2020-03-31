<?php

declare(strict_types=1);

namespace SousedskaPomoc;

use Nette\Configurator;

class Bootstrap
{
    public static function boot(): Configurator
    {
        $configurator = new Configurator;

        //$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
        $configurator->setDebugMode(getenv('DEBUG') === 'on');
        $configurator->enableTracy(__DIR__ . '/../var/log');

        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory(__DIR__ . '/../var/temp');

        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();

        $configurator
            ->addConfig(__DIR__ . '/../config/common.neon')
            ->addConfig(__DIR__ . '/../config/local.neon');

        return $configurator;
    }


    public static function bootForTests(): Configurator
    {
        $configurator = self::boot();
        \Tester\Environment::setup();
        return $configurator;
    }
}
