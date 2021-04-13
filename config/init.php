<?php
require '../vendor/autoload.php';
require '../libs/functions.php';


use Core\Router;
use Whoops\Run;


define('ROOT', dirname(__DIR__));
define('APP', ROOT . '/app');
define('PUBLIC', ROOT . '/public');
define('CORE', ROOT . '/core');


$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();


session_start();


$whoops = new Run;
$whoops->pushHandler(new Whoops\Handler\PrettyPageHandler());
$whoops->register();



//$d = new \App\Models\DumpD();
//$d->DumpD();


//User routers
Router::addRoute(regexp: '^api/order:POST$',     route: ['method' => 'POST', 'controller' => 'User', 'action' => 'create']);
Router::addRoute(regexp: '^api/order:GET$',      route: ['method' => 'GET', 'controller' => 'User', 'action' => 'getAll']);
Router::addRoute(regexp: '^api/order:GET\\?id$', route: ['method' => 'GET', 'controller' => 'User', 'action' => 'getById']);
Router::addRoute(regexp: '^api/order:PUT\\?id$', route: ['method' => 'PUT', 'controller' => 'User', 'action' => 'update']);
Router::addRoute(regexp: '^api/order:DELETE$',   route: ['method' => 'DELETE', 'controller' => 'User', 'action' => 'delete']);

//Auth Routes
Router::addRoute(regexp: '^api/login:POST$',     route: ['method' => 'POST', 'controller' => 'Authentication', 'action' => 'login']);
Router::addRoute(regexp: '^api/auth:POST$',      route: ['method' => 'POST', 'controller' => 'Authentication', 'action' => 'auth']);
Router::addRoute(regexp: '^api/createpassword:POST$',      route: ['method' => 'POST', 'controller' => 'Authentication', 'action' => 'createPassword']);

//Admin routes
Router::addRoute(regexp: '^api/admin/cabinet$',  route: ['method' => 'GET', 'controller' => 'Admin', 'action' => 'cabinet', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin/report$',   route: ['method' => 'GET', 'controller' => 'Admin', 'action' => 'report', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin:GET$',      route: ['method' => 'GET', 'controller' => 'Admin', 'action' => 'getAll', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin:GET\\?id$', route: ['method' => 'GET', 'controller' => 'Admin', 'action' => 'getById', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin:PUT$',      route: ['method' => 'PUT', 'controller' => 'Admin', 'action' => 'update', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin:DELETE$',   route: ['method' => 'DELETE', 'controller' => 'Admin', 'action' => 'delete', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin/create-user:POST$',   route: ['method' => 'POST', 'controller' => 'Admin', 'action' => 'createUser', 'prefix' => 'admin\\']);


//Other routers
Router::addRoute(regexp: '^api/calendar:GET$',   route: ['method' => 'GET', 'controller' => 'Subrequest', 'action' => 'calendar']);
Router::addRoute(regexp: '^api/departments:GET$',   route: ['method' => 'GET', 'controller' => 'Subrequest', 'action' => 'getDepartmentsList']);


Router::dispatch(url: $_SERVER['REQUEST_URI'], method: $_SERVER['REQUEST_METHOD']);

