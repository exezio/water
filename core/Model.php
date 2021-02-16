<?php


namespace Core;


use Core\lib\DataBase;
use MongoDB\Client;

abstract class Model
{
    /**Database name
     * @var string
     */
    protected string $dataBaseName = 'water';

    /**Client for work on database
     * @var Client
     */
    protected object $mongoClient;

    /**Login attributes
     * @var array $attributesCheckLogin
     */
    protected array $attributesCheckLogin;

    /**Authentication attributes
     * @var array
     */
    protected array $attributesAuth;

    public function __construct()
    {
        $database = new DataBase();
        $this->mongoClient = $database->getClient()->selectDatabase($this->dataBaseName);
    }




}