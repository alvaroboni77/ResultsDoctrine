<?php

use MiW\Results\Entity\Result;
use MiW\Results\Utils;

require __DIR__ . '/../../vendor/autoload.php';

// Carga las variables de entorno
Utils::loadEnv(__DIR__ . '/../../');

$entityManager = Utils::getEntityManager();

$fichero = basename(__FILE__);
if( $argc != 2) {
    echo <<<MARCA_FIN
        Usage: $fichero <ID RESULT>
    MARCA_FIN;
    exit(0);
}

$resultId = (int) $argv[1];

/** @var Result $result */
$result = $entityManager
    ->getRepository(Result::class)
    ->findOneBy(["id" => $resultId]);

if (null == $result) {
    echo "Result #$resultId not found";
    exit(0);
}

try {
    $entityManager->remove($result);
    $entityManager->flush();
//    echo "Removed result with ID #" . $result->getId() . PHP_EOL;
    echo "Removed result with Username: " .$result->getUser() . PHP_EOL;
}catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}
