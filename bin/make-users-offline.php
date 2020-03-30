<?php

declare(strict_types=1);

use SousedskaPomoc\Components\Mail;
use SousedskaPomoc\Model\UserManager;

require __DIR__.'/../vendor/autoload.php';

$container = SousedskaPomoc\Bootstrap::boot()
	->createContainer();

/** @var UserManager $manager */
$manager = $container->getByType(SousedskaPomoc\Model\UserManager::class);

foreach($manager->findAllOnlineUsers() as $user) {
	$manager->setOnline($user->id, false);
}
