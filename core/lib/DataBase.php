<?php


namespace Core\lib;


use MongoDB\Client;
use MongoDB\Exception\Exception;


class DataBase
{
    use SingletonTrait;

    private object $mongoClient;

    public function __construct()
    {
        $config = $_ENV['DB'] . $_ENV['DB_HOST'] . $_ENV['DB_PORT'];
        try {
            $this->mongoClient = new Client($config);
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }

    public function connect()
    {
        return $this->mongoClient;
    }

}