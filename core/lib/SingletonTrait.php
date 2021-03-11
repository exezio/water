<?php


namespace Core\lib;


trait SingletonTrait
{
    public static $instance;

    public static function instance(): object
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

}