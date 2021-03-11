<?php


namespace Core\lib;


trait SingletonTrait
{
    public static ?object $instance = null;

    public static function instance(): object
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function connect()
    {
        return self::$mongoClient;
    }
}