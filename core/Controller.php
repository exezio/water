<?php


namespace Core;


abstract class Controller
{

    public function __construct(
        protected array $route,
        protected ?array $getParams,
        protected string $method
    ){}

}