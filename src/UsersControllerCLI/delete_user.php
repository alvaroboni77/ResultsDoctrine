<?php

use MiW\Results\Entity\User;
use MiW\Results\Utils;

require __DIR__ . '/../../vendor/autoload.php';

// Carga las variables de entorno
Utils::loadEnv(__DIR__ . '/../../');

$entityManager = Utils::getEntityManager();

if( $argc != 2) {
    $fichero = basename(__FILE__);
    echo <<<MARCA_FIN
        Usage: $fichero <ID USUARIO>
    MARCA_FIN;
    exit(0);
}

$userId = (int) $argv[1];

/** @var User $user */
$user = $entityManager
    ->getRepository(User::class)
    ->findOneBy(["id" => $userId]);

if (null == $user) {
    echo "User #$userId not found";
    exit(0);
}

try {
    $entityManager->remove($user);
    $entityManager->flush();
//    echo "Removed user with ID #" . $user->getId() . " Username: " .$user->getUsername() . PHP_EOL;
    echo "Removed user with Username: " .$user->getUsername() . PHP_EOL;
}catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}
