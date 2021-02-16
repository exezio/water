<?php
require '../vendor/autoload.php';
require '../libs/functions.php';

use Core\Router;
use Whoops\Run;

define('ROOT', dirname(__DIR__));
define('APP', ROOT . '/app');
define('PUBLIC', ROOT . '/public');
define('CORE', ROOT . '/core');


session_start();


// Пакет PHP errors
$whoops = new Run;
$whoops->pushHandler(new Whoops\Handler\PrettyPageHandler());
$whoops->register();


// Пакет для работы с .env
$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();


//User routers
Router::addRoute(regexp: '^api/calendar:GET$',   route: ['method' => 'POST', 'controller' => 'User', 'action' => 'calendar']);

Router::addRoute(regexp: '^api/order:POST$',     route: ['method' => 'POST', 'controller' => 'User', 'action' => 'create']);
Router::addRoute(regexp: '^api/order:GET$',      route: ['method' => 'GET', 'controller' => 'User', 'action' => 'getAll']);
Router::addRoute(regexp: '^api/order:GET\\?id$', route: ['method' => 'GET', 'controller' => 'User', 'action' => 'getById']);
Router::addRoute(regexp: '^api/order:PUT\\?id$', route: ['method' => 'PUT', 'controller' => 'User', 'action' => 'update']);
Router::addRoute(regexp: '^api/order:DELETE$',   route: ['method' => 'DELETE', 'controller' => 'User', 'action' => 'delete']);
Router::addRoute(regexp: '^api/login:POST$',     route: ['method' => 'POST', 'controller' => 'User', 'action' => 'login']);
Router::addRoute(regexp: '^api/auth:POST$',      route: ['method' => 'POST', 'controller' => 'User', 'action' => 'auth']);

//Admin routes
Router::addRoute(regexp: '^api/admin/cabinet$',  route: ['method' => 'GET', 'controller' => 'Admin', 'action' => 'cabinet', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin/report$',   route: ['method' => 'GET', 'controller' => 'Admin', 'action' => 'report', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin:GET$',      route: ['method' => 'GET', 'controller' => 'Admin', 'action' => 'getAll', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin:GET\\?id$', route: ['method' => 'GET', 'controller' => 'Admin', 'action' => 'getById']);
Router::addRoute(regexp: '^api/admin:PUT$',      route: ['method' => 'PUT', 'controller' => 'Admin', 'action' => 'update']);
Router::addRoute(regexp: '^api/admin:DELETE$',   route: ['method' => 'DELETE', 'controller' => 'Admin', 'action' => 'delete']);

Router::dispatch(url: $_SERVER['REQUEST_URI'], method: $_SERVER['REQUEST_METHOD']);


