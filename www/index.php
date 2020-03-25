<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$container = SousedskaPomoc\Bootstrap::boot()->createContainer();

$isApi = substr($_SERVER['REQUEST_URI'], 0, 4) === '/api';
if ($isApi) {
	$container->getByType(\Apitte\Core\Application\IApplication::class)->run();

} else {
	$container->getByType(Nette\Application\Application::class)->run();
}
