<?php


namespace Core\lib;


use MongoDB\Client;
use MongoDB\Exception\Exception;


class DataBase
{
    use SingletonTrait;

    private static ?object $mongoClient;

    public function __construct()
    {
        $config = $_ENV['DB'] . $_ENV['DB_HOST'] . $_ENV['DB_PORT'];
        try {
            self::$mongoClient = new Client($config);
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }



}