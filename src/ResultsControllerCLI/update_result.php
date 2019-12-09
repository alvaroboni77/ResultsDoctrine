<?php

use MiW\Results\Entity\Result;
use MiW\Results\Entity\User;
use MiW\Results\Utils;

require __DIR__ . '/../../vendor/autoload.php';

// Carga las variables de entorno
Utils::loadEnv(__DIR__ . '/../../');

$entityManager = Utils::getEntityManager();

if( $argc < 4) {
    $fichero = basename(__FILE__);
    echo <<<MARCA_FIN
        Usage: $fichero <ID RESULT> <CAMPO> <VALOR> <CAMPO> <VALOR> ...
    MARCA_FIN;
    exit(0);
}

$resultId = (int) $argv[1];
$arrUpdate = [];
$n = 2;
while( !empty($argv[$n]) ) {
    $arrUpdate[$argv[$n]] = $argv[$n+1];
    $n += 2;
}

/** @var Result $result */
$result = $entityManager
    ->getRepository(Result::class)
    ->findOneBy(["id" => $resultId]);

if (null == $result) {
    echo "Result $resultId not found";
    exit(0);
}

foreach ($arrUpdate as $key => $value) {
    if( $key == "result" && $value != "" ) {
        $result->setResult($value);
    } elseif ( $key == "user_id" && $value != "" ) {
        /** @var User $new_user */
        $new_user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(["id" => $value]);
        $result->setUser($new_user);
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
    echo "Updated result with ID #" . $result->getId() . PHP_EOL;
}catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}
