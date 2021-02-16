<?php


namespace Core\lib;


use MongoDB\Client;
use MongoDB\Exception\Exception;


class DataBase
{

    private ?object $mongoClient;


    public function __construct()
    {
        $config = $_ENV['DB'] . $_ENV['DB_HOST'] . $_ENV['DB_PORT'];
        try {
            $this->mongoClient = new Client($config);
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }

    public function getClient(): object
    {
        return $this->mongoClient;
    }

}