<?php


namespace Core;


class Router
{

    private static $routes = [];
    private static $route = [];
    private static $getParams = [];
    private static $method = '';
    private static $url = '';


    public static function addRoute($regexp, $route = [])
    {
        self::$routes[$regexp] = $route;
    }

    public static function getRoutes()
    {
        return self::$routes;
    }

    public static function getRoute()
    {
        return self::$route;
    }

    public static function getParams()
    {
        return self::$getParams;
    }

    public static function getMethod()
    {
        return self::$method;
    }

    public static function dispatch($url, $method)
    {
        if (self::matchRoute($url, $method)) {
            $controller = 'App\Controllers\\' . self::$route['prefix'] . self::$route['controller'] . 'Controller';
            $action = self::$route['action'] . 'Action';
            if (class_exists($controller)) {
                $controllerObj = new $controller(self::$route, self::$getParams, self::$method);
                if (method_exists($controllerObj, $action)) $controllerObj->$action();
            } else {
                http_response_code(404);
            }
        }
    }

    private static function matchRoute($url, $method)
    {
        self::parseUrl($url, $method);
        $getParams = self::$getParams ? '?' . array_key_first(self::$getParams) : null;
        $url = self::$url . ":$method" . $getParams;
        foreach (self::$routes as $pattern => $route) {
            if (preg_match("#$pattern#", $url, $matches)) {
                foreach ($matches as $key => $value) {
                    if (is_string($key)) $route[$key] = $value;
                }
                self::$route = $route;
                return true;
            }
        }
        return false;
    }

    private static function parseUrl($url, $method)
    {
        $url = parse_url($url);
        if (isset($url['query'])) {
            parse_str($url['query'], $paramsArray);
            self::$getParams = $paramsArray;
        }
        self::$url = trim($url['path'], '/');
        self::$method = $method;
    }

}