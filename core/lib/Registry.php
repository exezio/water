<?php


namespace Core\lib;


class Registry
{

    public static $container = [];

    public static function set($key, $value): void
    {
        if (!isset(self::$container[$key])) {
            self::$container[$key] = $value;
        }
    }

    public static function get($key): mixed
    {
        if (isset(self::$container[$key])) {
            return self::$container[$key];
        }
        return false;
    }

    public static function delete($key): bool
    {
        if (isset(self::$container[$key])) {
            unset(self::$container[$key]);
            return true;
        }
        return false;
    }

}