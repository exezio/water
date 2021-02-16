<?php


namespace App\Models;


class Auth extends User
{
    /**Name of collection of database
     * @var string
     */
    private string $collectionName = 'users';

    /**Check user in database
     * @return bool
     */
    public function checkLogin(): bool
    {
        if ($this->validate(attributes: 'attributesCheckLogin', rules: 'rulesCheckLogin')) {
            $login = $this->attributesLogin['login'];
            $usersDB = $this->mongoClient->selectCollection($this->collectionName);
//            $usersDB->insertMany()
        }
    }

    /**Correctness check login and password on database
     * @return bool
     */
    public function auth(): bool
    {
        $db = $this->mongoClient->selectCollection('departments');
        $res = $db->findOne(array('department' => 'Цех 04'));
        debug($res['phone']);
    }


}