<?php
require '../vendor/autoload.php';
require '../libs/functions.php';

use Whoops\Run;
use Core\Router;

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
Router::addRoute('^order:POST$', ['method' => 'POST','controller' => 'User', 'action' => 'create']);
Router::addRoute('^order:GET$', ['method' => 'GET','controller' => 'User', 'action' => 'getAll']);
Router::addRoute('^order:GET\\?id$', ['method' => 'GET','controller' => 'User', 'action' => 'getById']);
Router::addRoute('^order:PUT$', ['method' => 'PUT','controller' => 'User', 'action' => 'update']);
Router::addRoute('^order:DELETE$', ['method' => 'DELETE','controller' => 'User', 'action' => 'delete']);

//Admin routes
Router::addRoute('^admin/cabinet$', ['method' => 'GET', 'controller' => 'Admin', 'action' => 'cabinet', 'prefix' => 'admin\\']);
Router::addRoute('^admin/report$', ['method' => 'GET', 'controller' => 'Admin', 'action' => 'report', 'prefix' => 'admin\\']);
Router::addRoute('^admin:GET$', ['method' => 'GET','controller' => 'Admin', 'action' => 'getAll', 'prefix' => 'admin\\']);
Router::addRoute('^admin:GET\\?id$', ['method' => 'GET','controller' => 'Admin', 'action' => 'getById']);
Router::addRoute('^admin:PUT$', ['method' => 'PUT','controller' => 'Admin', 'action' => 'update']);
Router::addRoute('^admin:DELETE$', ['method' => 'DELETE','controller' => 'Admin', 'action' => 'delete']);
//Router::addRoute('^(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$');

Router::dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);