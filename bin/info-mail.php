<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';


$container = SousedskaPomoc\Bootstrap::boot()
    ->createContainer();

[, $name, $email, $password] = $_SERVER['argv'];

$manager = $container->getByType(SousedskaPomoc\Model\UserManager::class);
$mail = $container->getByType(SousedskaPomoc\Components\Mail::class);

try {
    $users = $manager->fetchAllUsersWithNoPass();
    foreach ($users as $user) {
        $link = 'https://sousedskapomoc.cz/change-password?locale=cs&hash='.$user['emailCode'];
        if ($user['role'] == 'coordinator') {
            $mail->sendCoordinatorMail($user['personEmail'], $link);
        } else {
            if ($user['role'] == 'seamstress') {
                $mail->sendSeamstressMail($user['personEmail'], $link);
            } else {
                if ($user['role'] == 'operator') {
                    $mail->sendOperatorMail($user['personEmail'], $link);
                } else {
                    if ($user['role'] == 'courier') {
                        $mail->sendCourierMail($user['personEmail'], $link);
                    }
                }
            }
        }
        echo "Sending to ".$user['personEmail']."\n";
    }
    echo "Mails was updated.\n";

} catch (SousedskaPomoc\Model\DuplicateNameException $e) {
    echo "Error: duplicate name.\n";
    exit(1);
}