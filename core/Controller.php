<?php


namespace Core;


abstract class Controller
{

    protected $route;
    protected $getParams;
    protected $method;

    public function __construct($route, $getParams, $method)
    {
        $this->route = $route;
        $this->getParams = $getParams;
        $this->method = $method;
    }

}