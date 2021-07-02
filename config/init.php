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


//User routers
Router::addRoute(regexp: '^api/order:POST$',     route: ['method' => 'POST', 'controller' => 'User', 'action' => 'createOrder']);
Router::addRoute(regexp: '^api/order-current-month:GET$',      route: ['method' => 'GET', 'controller' => 'User', 'action' => 'getAllOrdersPerMonth']);
Router::addRoute(regexp: '^api/order:GET$',      route: ['method' => 'GET', 'controller' => 'User', 'action' => 'getAllDepartmentOrders']);
Router::addRoute(regexp: '^api/order-date:GET$',      route: ['method' => 'GET', 'controller' => 'User', 'action' => 'getAllOrdersByDate']);
Router::addRoute(regexp: '^api/order-id:GET$', route: ['method' => 'GET', 'controller' => 'User', 'action' => 'getOrderById']);
Router::addRoute(regexp: '^api/order-id:PUT$', route: ['method' => 'PUT', 'controller' => 'User', 'action' => 'updateOrderById']);
Router::addRoute(regexp: '^api/order-id:DELETE$',   route: ['method' => 'DELETE', 'controller' => 'User', 'action' => 'deleteOrder']);
Router::addRoute(regexp: '^api/info:GET$',     route: ['method' => 'GET', 'controller' => 'User', 'action' => 'getDepartmentInfo']);

//Auth Routes
Router::addRoute(regexp: '^api/login:POST$',     route: ['method' => 'POST', 'controller' => 'Authentication', 'action' => 'login']);
Router::addRoute(regexp: '^api/auth:POST$',      route: ['method' => 'POST', 'controller' => 'Authentication', 'action' => 'auth']);
Router::addRoute(regexp: '^api/createpassword:POST$',      route: ['method' => 'POST', 'controller' => 'Authentication', 'action' => 'createPassword']);

//Admin routes
Router::addRoute(regexp: '^api/admin/cabinet:GET$',  route: ['method' => 'GET', 'controller' => 'Admin', 'action' => 'cabinet', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin/report$',   route: ['method' => 'GET', 'controller' => 'Admin', 'action' => 'report', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin:GET$',      route: ['method' => 'GET', 'controller' => 'Admin', 'action' => 'getAll', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin:GET\\?id$', route: ['method' => 'GET', 'controller' => 'Admin', 'action' => 'getById', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin:PUT$',      route: ['method' => 'PUT', 'controller' => 'Admin', 'action' => 'update', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin:DELETE$',   route: ['method' => 'DELETE', 'controller' => 'Admin', 'action' => 'delete', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin/permission:POST$',  route: ['method' => 'POST', 'controller' => 'Admin', 'action' => 'addPermission', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin/permission:GET$',  route: ['method' => 'GET', 'controller' => 'Admin', 'action' => 'getPermissions', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin/permission:PUT$',  route: ['method' => 'PUT', 'controller' => 'Admin', 'action' => 'putPermission', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin/permission:DELETE$',  route: ['method' => 'DELETE', 'controller' => 'Admin', 'action' => 'deletePermission', 'prefix' => 'admin\\']);
Router::addRoute(regexp: '^api/admin/create-user:POST$',   route: ['method' => 'POST', 'controller' => 'Admin', 'action' => 'createUser', 'prefix' => 'admin\\']);

//Moderator Routes
Router::addRoute(regexp: '^api/moderator/cabinet:GET$',  route: ['method' => 'GET', 'controller' => 'Moderator', 'action' => 'cabinet', 'prefix' => 'moderator\\']);


//Other routers
Router::addRoute(regexp: '^api/calendar:GET$',   route: ['method' => 'GET', 'controller' => 'Subrequest', 'action' => 'calendar']);
Router::addRoute(regexp: '^api/departments:GET$',   route: ['method' => 'GET', 'controller' => 'Subrequest', 'action' => 'getDepartmentsList']);
Router::addRoute(regexp: '^refresh-token:GET$',   route: ['method' => 'GET', 'controller' => 'Subrequest', 'action' => 'refreshToken']);


Router::dispatch(url: $_SERVER['REQUEST_URI'], method: $_SERVER['REQUEST_METHOD']);

