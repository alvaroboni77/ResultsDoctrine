<?php

use MiW\Results\Entity\User;
use MiW\Results\Utils;

require __DIR__ . '/../../vendor/autoload.php';

// Carga las variables de entorno
Utils::loadEnv(__DIR__ . '/../../');

$entityManager = Utils::getEntityManager();

if( $argc < 4 || $argc > 5 ) {
    $fichero = basename( __FILE__ );
    echo <<<MARCA_FIN
        Usage: $fichero <USERNAME> <EMAIL> <PASSWORD> [<ENABLED>]
    MARCA_FIN;
    exit(0);
}

$username = (string) $argv[1];
$email = (string) $argv[2];
$password = (string) $argv[3];
$enabled = $argv[4] ?? true;

$user = new User();
$user->setUsername($username);
$user->setEmail($email);
$user->setPassword($password);
$user->setEnabled($enabled);
$user->setIsAdmin(false);

try {
    $entityManager->persist($user);
    $entityManager->flush();
    echo 'Created User with ID #' . $user->getId() . PHP_EOL;
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}