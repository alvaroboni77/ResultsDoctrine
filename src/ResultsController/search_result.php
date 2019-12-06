<?php

use MiW\Results\Entity\Result;
use MiW\Results\Utils;

require __DIR__ . '/../../vendor/autoload.php';

// Carga las variables de entorno
Utils::loadEnv(__DIR__ . '/../../');

$entityManager = Utils::getEntityManager();

$fichero = basename(__FILE__);
if( $argc < 2 || $argc > 3 ) {
    echo <<<MARCA_FIN
        Usage: $fichero <ID RESULTADO>
    MARCA_FIN;
    exit(0);
}

$resultId = (int) $argv[1];

/** @var Result $result */
$result = $entityManager
    ->getRepository(Result::class)
    ->findOneBy(["id" => $resultId]);

if (null == $result) {
    echo "Result $resultId not found";
    exit(0);
}

if (in_array('--json', $argv, true)) {
    echo json_encode($result, JSON_PRETTY_PRINT);
} else {
    echo PHP_EOL . sprintf(
            '  %2s: %20s %20s %20s' . PHP_EOL,
            'Id', 'Result:', 'Username:', 'Time:'
        );
    /** @var Result $result */
    $date = $result->getTime();
    echo sprintf(
        '- %2d: %20s %20s %20s',
        $result->getId(),
        $result->getResult(),
        $result->getUser(),
        $date->format('d-m-Y')
    ),
    PHP_EOL;
}