<?php

use MiW\Results\Entity\User;
use MiW\Results\Utils;

require __DIR__ . '/../../vendor/autoload.php';

// Carga las variables de entorno
Utils::loadEnv(__DIR__ . '/../../');

$entityManager = Utils::getEntityManager();

if( $argc < 2 || $argc > 3 ) {
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
    echo "Usuario $userId no encontrado";
    exit(0);
}

if (in_array('--json', $argv, true)) {
    echo json_encode($user, JSON_PRETTY_PRINT);
} else {
    echo PHP_EOL . sprintf(
            '  %2s: %20s %30s %7s %7s' . PHP_EOL,
            'Id', 'Username:', 'Email:', 'Admin:', 'Enabled:'
        );
    /** @var User $user */
    echo sprintf(
        '- %2d: %20s %30s %7s %7s',
        $user->getId(),
        $user->getUsername(),
        $user->getEmail(),
        ($user->isAdmin()) ? 'true' : 'false',
        ($user->isEnabled()) ? 'true' : 'false'
    ),
    PHP_EOL;
}