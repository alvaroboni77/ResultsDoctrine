<?php

use MiW\Results\Entity\User;
use MiW\Results\Utils;

require __DIR__ . '/../../vendor/autoload.php';

// Carga las variables de entorno
Utils::loadEnv(__DIR__ . '/../../');

$entityManager = Utils::getEntityManager();

if( $argc < 4) {
    $fichero = basename(__FILE__);
    echo <<<MARCA_FIN
        Usage: $fichero <ID USUARIO> <CAMPO> <VALOR> <CAMPO> <VALOR> ...
    MARCA_FIN;
    exit(0);
}

$userId = (int) $argv[1];
$arrUpdate = [];
$n = 2;
while( !empty($argv[$n]) ) {
    $arrUpdate[$argv[$n]] = $argv[$n+1];
    $n += 2;
}

/** @var User $user */
$user = $entityManager
    ->getRepository(User::class)
    ->findOneBy(["id" => $userId]);

if (null == $user) {
    echo "Usuario $userId no encontrado";
    exit(0);
}

foreach ($arrUpdate as $key => $value) {
    if( $key == "username" && $value != "" ) {
        $user->setUsername($value);
    } elseif ( $key == "email" && $value != "" ) {
        $user->setEmail($value);
    } elseif ( $key == "enabled" && $value != "" ) {
        $val = ($value === "true");
        $user->setEnabled($val);
    } elseif ( $key == "admin" && $value != "" ) {
//    $val = $value === "true" ? true : false;
        $val = ($value === "true");
        $user->setIsAdmin($val);
    } elseif ( $key == "password" && $value != "" ) {
        $user->setPassword($value);
    } elseif ( $value == "" ) {
        echo "Value cannot be empty";
        exit(0);
    } else {
        echo $key . " is not a valid key";
        exit(0);
    }
}

try {
    $entityManager->flush();
    echo "Updated user with ID #" . $user->getId() . PHP_EOL;
}catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}
