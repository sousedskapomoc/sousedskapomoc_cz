<?php declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

$container = SousedskaPomoc\Bootstrap::boot()->createContainer();
$app = $container->getByType(\Contributte\Console\Application::class);
assert($app instanceof \Contributte\Console\Application);
exit($app->run());
