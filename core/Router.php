<?php


namespace Core;



class Router
{
    /**Array of preassigned routes
     * @var array
     */
    private static array $routes;

    /** Array to fill when matches are found
     * @var array
     */
    private static array $route;

    /**Array of get-params if they exists
     * @var array|null
     */
    private static ?array $getParams = null;

    /**Request method
     * @var string
     */
    private static string $method;

    /**Url
     * @var string
     */
    private static string $url;

    /**Function for adding routes
     * @param string $regexp
     * @param array $route
     */
    public static function addRoute(string $regexp, array $route = []) : void
    {
        self::$routes[$regexp] = $route;
    }

    public static function getRoutes() : array
    {
        return self::$routes;
    }

    public static function getRoute() : array
    {
        return self::$route;
    }

    public static function getParams() : array
    {
        return self::$getParams;
    }

    public static function getMethod() : string
    {
        return self::$method;
    }

    public static function dispatch(string $url, string $method) : void
    {
        if (self::matchRoute($url, $method)) {
            $controller = 'App\Controllers\\' . (isset(self::$route['prefix']) ? self::$route['prefix'] : '') . self::$route['controller'] . 'Controller';
            $action = self::$route['action'] . 'Action';
            if (class_exists($controller)) {
                $controllerObj = new $controller(self::$route, self::$getParams, self::$method);

                if (method_exists($controllerObj, $action)) $controllerObj->$action();
            }
        }else {
            http_response_code(404);
            echo json_encode(array("error" => array(
                "code" => 404,
                "message" => "Ресурс не найден",
                "error_code" => 1
            )), JSON_UNESCAPED_UNICODE);
        }
    }

    /**Finding route matches
     * @param string $url
     * @param string $method
     * @return bool
     */
    private static function matchRoute(string $url, string $method) : bool
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

    /**Converts url to the required form and extracts get parameters
     * @param string $url
     * @param string $method
     */
    private static function parseUrl(string $url, string $method) : void
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