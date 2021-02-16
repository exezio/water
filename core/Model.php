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

    /**Filling the array with user data
     * @param array $data
     * @param string $subject
     * @return void
     */
    public  function loadAttributes(array $data, string $subject) : void
    {
        foreach ($this->$subject as $item=>$value)
        {
            if(isset($data[$item])) $this->$subject[$item] = $data[$item];
        }
    }


}