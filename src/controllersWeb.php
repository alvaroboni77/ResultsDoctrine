<?php

use MiW\Results\Utils;
use MiW\Results\Entity\User;
use MiW\Results\Entity\Result;

Utils::loadEnv(__DIR__ . '/../');

function showHomePage() {
    global $routes;

    $rutaListadoUsuarios = $routes->get('ruta_list_users')->getPath();
    $rutaMostrarUsuario = $routes->get('ruta_show_user')->getPath();
    $rutaNuevoUsuario = $routes->get('ruta_new_user')->getPath();
    $rutaActualizarUsuario = $routes->get('ruta_update_user')->getPath();
    $rutaEliminarUsuario = $routes->get('ruta_delete_user')->getPath();
    $rutaListadoResultados = $routes->get('ruta_list_results')->getPath();
    $rutaMostrarResultado = $routes->get('ruta_show_result')->getPath();
    $rutaNuevoResultado = $routes->get('ruta_new_result')->getPath();
    $rutaActualizarResultado = $routes->get('ruta_update_result')->getPath();
    $rutaEliminarResultado = $routes->get('ruta_delete_result')->getPath();

    echo    "<h1>Página principal</h1>".
        "<p>¿Qué acción desea realizar con respecto a los usuarios?</p>".
        "<ul>".
        "<li><a href='".$rutaListadoUsuarios."'>Consultar todos los usuarios</a></li><br>".
        "<li><a href='".$rutaMostrarUsuario."'>Consultar un usuario</a></li><br>".
        "<li><a href='".$rutaNuevoUsuario."'>Crear un nuevo usuario</a></li><br>".
        "<li><a href='".$rutaActualizarUsuario."'>Actualizar un usuario</a></li><br>".
        "<li><a href='".$rutaEliminarUsuario."'>Eliminar un usuario</a></li><br>".
        "</ul>".
        "<p>¿Qué acción desea realizar con respecto a los resultados?</p>".
        "<ul>".
        "<li><a href='".$rutaListadoResultados."'>Consultar todos los resultados</a></li><br>".
        "<li><a href='".$rutaMostrarResultado."'>Consultar un resultado</a></li><br>".
        "<li><a href='".$rutaNuevoResultado."'>Crear un resultado</a></li><br>".
        "<li><a href='".$rutaActualizarResultado."'>Actualizar un resultado</a></li><br>".
        "<li><a href='".$rutaEliminarResultado."'>Eliminar un resultado</a></li><br>".
        "</ul>";
}

function listUsers(?string $json = null) {
    global $routes;

    $entityManager = Utils::getEntityManager();
    $users = $entityManager
        ->getRepository(User::class)
        ->findAll();
    $rutaHome = $routes->get('ruta_raiz')->getPath();
    echo "<br><a href='$rutaHome'>Volver a la página principal</a>";
    echo ($json)
        ? "<pre>".json_encode($users, JSON_PRETTY_PRINT)."</pre>"
        :var_dump($users);
}

function showUser(?string $json = null) {
    global $routes;

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $rutaMostrarUsuario = $routes->get('ruta_show_user')->getPath();
        echo <<<___MARCA_FIN
            <form method="POST" action="$rutaMostrarUsuario">
                ID de usuario: <input type="text" name="user_id"><br>                            
                <input type="submit" value="Enviar"> 
            </form>
        ___MARCA_FIN;

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $entityManager = Utils::getEntityManager();

        $user_id = $_POST['user_id'];

        /** @var User $user */
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(["id" => $user_id]);

        if (null == $user) {
            echo "Usuario $user_id no encontrado";
            exit(0);
        }

        echo "<pre>".json_encode($user, JSON_PRETTY_PRINT)."</pre>";

        $rutaHome = $routes->get('ruta_raiz')->getPath();
        echo "<br><a href='$rutaHome'>Volver a la página principal</a>";
    }
}

function newUser() {
    global $routes;

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $rutaNuevoUsuario = $routes->get('ruta_new_user')->getPath();
        echo <<<___MARCA_FIN
            <form method="POST" action="$rutaNuevoUsuario">
                Nombre de usuario: <input type="text" name="username" required><br>
                Email: <input type="email" name="email" required><br>
                Contraseña: <input type="password" name="password" required><br>
                <input type="radio" name="enabled" value="1">Activo<input type="radio" name="enabled" value="0">Inactivo<br>
                <input type="radio" name="admin" value="1">Administrador<input type="radio" name="admin" value="0">Usuario<br>            
                <input type="submit" value="Enviar"> 
            </form>
    ___MARCA_FIN;

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $entityManager = Utils::getEntityManager();

        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $enabled = $_POST['enabled'];
        $admin = $_POST['admin'];
        $user = new User($username, $email, $password, $enabled, $admin);

        try {
            $entityManager->persist($user);
            $entityManager->flush();
            echo 'Created User with ID #' . $user->getId() . PHP_EOL;
        } catch (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }

        $rutaHome = $routes->get('ruta_raiz')->getPath();
        echo "<br><a href='$rutaHome'>Volver a la página principal</a>";
    }
}

function updateUser() {
    global $routes;

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $rutaActualizarUsuario = $routes->get('ruta_update_user')->getPath();
        echo <<<___MARCA_FIN
            <form method="POST" action="$rutaActualizarUsuario">
                ID de usuario: <input type="text" name="user_id"><br>
                Nombre de usuario: <input type="text" name="username"><br>
                Email: <input type="email" name="email"><br>
                Contraseña: <input type="password" name="password"><br>
                <input type="radio" name="enabled" value="1">Activo<input type="radio" name="enabled" value="0">Inactivo<br>
                <input type="radio" name="admin" value="1">Administrador<input type="radio" name="admin" value="0">Usuario<br>               
                <input type="submit" value="Enviar"> 
            </form>
    ___MARCA_FIN;

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $entityManager = Utils::getEntityManager();

        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $enabled = $_POST['enabled'];
        $admin = $_POST['admin'];

        /** @var User $user */
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(["id" => $user_id]);

        if (null == $user) {
            echo "Usuario $user_id no encontrado";
            exit(0);
        }

        if( $username != "" ) {
            $user->setUsername($username);
        }
        if( $email != "" ) {
            $user->setEmail($email);
        }
        if( $password != "" ) {
            $user->setPassword($password);
        }
        if( $enabled != "" ) {
            $user->setEnabled($enabled);
        }
        if( $admin != "" ) {
            $user->setIsAdmin($admin);
        }


        try {
            $entityManager->flush();
            echo "Updated user with ID #" . $user->getId() . PHP_EOL;
        }catch (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }

        $rutaHome = $routes->get('ruta_raiz')->getPath();
        echo "<br><a href='$rutaHome'>Volver a la página principal</a>";
    }
}

function deleteUser() {
    global $routes;

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $rutaEliminarUsuario = $routes->get('ruta_delete_user')->getPath();
        echo <<<___MARCA_FIN
            <form method="POST" action="$rutaEliminarUsuario">
                ID de usuario: <input type="text" name="user_id"><br>                            
                <input type="submit" value="Enviar"> 
            </form>
        ___MARCA_FIN;

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $entityManager = Utils::getEntityManager();

        $user_id = $_POST['user_id'];

        /** @var User $user */
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(["id" => $user_id]);

        if (null == $user) {
            echo "Usuario $user_id no encontrado";
            exit(0);
        }

        try {
            $entityManager->remove($user);
            $entityManager->flush();
            echo "Eliminado usuario con Username: " .$user->getUsername() . PHP_EOL;
        }catch (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }

        $rutaHome = $routes->get('ruta_raiz')->getPath();
        echo "<br><a href='$rutaHome'>Volver a la página principal</a>";
    }
}

function listResults(?string $json = null) {
    global $routes;

    $entityManager = Utils::getEntityManager();
    $results = $entityManager
        ->getRepository(Result::class)
        ->findAll();
    $rutaHome = $routes->get('ruta_raiz')->getPath();
    echo "<br><a href='$rutaHome'>Volver a la página principal</a>";
    echo ($json)
        ? "<pre>".json_encode($results, JSON_PRETTY_PRINT)."</pre>"
        :var_dump($results);

}

function showResult(?string $json = null) {
    global $routes;

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $rutaMostrarResultado = $routes->get('ruta_show_result')->getPath();
        echo <<<___MARCA_FIN
            <form method="POST" action="$rutaMostrarResultado">
                ID de resultado: <input type="text" name="result_id"><br>                            
                <input type="submit" value="Enviar"> 
            </form>
        ___MARCA_FIN;

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $entityManager = Utils::getEntityManager();

        $result_id = $_POST['result_id'];

        /** @var Result $result */
        $result = $entityManager
            ->getRepository(Result::class)
            ->findOneBy(["id" => $result_id]);

        if (null == $result) {
            echo "Resultado #$result_id no encontrado";
            exit(0);
        }

        echo "<pre>".json_encode($result, JSON_PRETTY_PRINT)."</pre>";

        $rutaHome = $routes->get('ruta_raiz')->getPath();
        echo "<br><a href='$rutaHome'>Volver a la página principal</a>";
    }
}

function newResult() {
    global $routes;

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $rutaNuevoResultado = $routes->get('ruta_new_result')->getPath();
        echo <<<___MARCA_FIN
            <form method="POST" action="$rutaNuevoResultado">
                Result: <input type="number" name="result" required><br>
                User ID: <input type="number" name="user_id" required><br>   
                <input type="submit" value="Enviar"> 
            </form>
        ___MARCA_FIN;

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $entityManager = Utils::getEntityManager();

        $newResult = $_POST['result'];
        $user_id = $_POST['user_id'];
        $newTimestamp = new DateTime('now');

        /** @var User $user */
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['id' => $user_id]);
        if (null === $user) {
            echo "Usuario $user_id no existe" . PHP_EOL;
            exit(0);
        }

        $result = new Result($newResult, $user, $newTimestamp);

        try {
            $entityManager->persist($result);
            $entityManager->flush();
            echo 'Created Result with ID ' . $result->getId()
                . ' USER ' . $user->getUsername() . PHP_EOL;
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }

        $rutaHome = $routes->get('ruta_raiz')->getPath();
        echo "<br><a href='$rutaHome'>Volver a la página principal</a>";
    }
}

function updateResult() {
    global $routes;

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $rutaActualizarResultado = $routes->get('ruta_update_result')->getPath();
        echo <<<___MARCA_FIN
            <form method="POST" action="$rutaActualizarResultado">
                ID de resultado: <input type="number" name="result_id"><br>
                Resultado: <input type="number" name="result"><br>
                ID de usuario: <input type="number" name="user_id"><br>           
                <input type="submit" value="Enviar"> 
            </form>
    ___MARCA_FIN;

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $entityManager = Utils::getEntityManager();

        $result_id = $_POST['result_id'];
        $result_val = $_POST['result'];
        $user_id = $_POST['user_id'];

        /** @var Result $result */
        $result = $entityManager
            ->getRepository(Result::class)
            ->findOneBy(["id" => $result_id]);

        if (null == $result) {
            echo "Result #$result_id not found";
            exit(0);
        }

        if( $result_val != "" ) {
            $result->setResult($result_val);
        }
        if( $user_id != "" ) {
            /** @var User $new_user */
            $new_user = $entityManager
                ->getRepository(User::class)
                ->findOneBy(["id" => $user_id]);
            if (null == $new_user) {
                echo "Usuario #$user_id not found";
                exit(0);
            }
            $result->setUser($new_user);
        }


        try {
            $entityManager->flush();
            echo "Updated result with ID #" . $result->getId() . PHP_EOL;
        }catch (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }

        $rutaHome = $routes->get('ruta_raiz')->getPath();
        echo "<br><a href='$rutaHome'>Volver a la página principal</a>";
    }
}

function deleteResult() {
    global $routes;

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $rutaEliminarResultado = $routes->get('ruta_delete_result')->getPath();
        echo <<<___MARCA_FIN
            <form method="POST" action="$rutaEliminarResultado">
                ID de resultado: <input type="text" name="result_id"><br>                            
                <input type="submit" value="Enviar"> 
            </form>
        ___MARCA_FIN;

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $entityManager = Utils::getEntityManager();

        $result_id = $_POST['result_id'];

        /** @var Result $result */
        $result = $entityManager
            ->getRepository(Result::class)
            ->findOneBy(["id" => $result_id]);

        if (null == $result) {
            echo "Resultado #$result_id no encontrado";
            exit(0);
        }

        try {
            echo "Eliminado resultado con ID #" .$result->getId() . PHP_EOL;
            $entityManager->remove($result);
            $entityManager->flush();
        }catch (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }

        $rutaHome = $routes->get('ruta_raiz')->getPath();
        echo "<br><a href='$rutaHome'>Volver a la página principal</a>";
    }
}