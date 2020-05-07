<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';


$container = SousedskaPomoc\Bootstrap::boot()
    ->createContainer();

[, $name, $email, $password] = $_SERVER['argv'];

$manager = $container->getByType(SousedskaPomoc\Model\UserManager::class);

try {
    $users = $manager->fetchAllUsers();
    foreach ($users as $user) {
        $hash = md5($user['personEmail']);
        $manager->setUserCode($user['id'], $hash);
    }
    echo "Users were updated.\n";

} catch (SousedskaPomoc\Model\DuplicateNameException $e) {
    echo "Error: duplicate name.\n";
    exit(1);
}