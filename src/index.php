<?php
require_once '../vendor/autoload.php';
require_once './controllersWeb.php';

use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\RouteCollection;

const DIRECTORIES = [ __DIR__ . '/../config' ];
const ROUTES_FILE = 'rutas.yml';

$locator = new FileLocator(DIRECTORIES);
$loader  = new YamlFileLoader($locator);
/** @var RouteCollection $routes */
$routes  = $loader->load(ROUTES_FILE);

$requestContext = new RequestContext(filter_input(INPUT_SERVER, 'REQUEST_URI'));

$matcher = new UrlMatcher($routes, $requestContext);

$path_info = filter_input(INPUT_SERVER, 'PATH_INFO') ?? '/';

try {
    $parameters = $matcher->match($path_info);
    $action = $parameters['_controller'];
    $param1 = $parameters['json'] ?? null;
    $param2 = $parameters['id'] ?? null;
    $action($param1, $param2);

} catch (ResourceNotFoundException $e) {
    echo 'Caught exception: The resource could not be found' . PHP_EOL;
} catch (MethodNotAllowedException $e) {
    echo 'Caught exception: the resource was found but the request method is not allowed'. PHP_EOL;
}

