<?php


namespace App\Models;


use Core\Model;

class CreateUser extends Model
{
    private mixed $departments;
    private mixed $users;


    public function createUser()
    {
        echo "kek";
    }
//$depart = $this->mongoClient->selectCollection('water', 'departments');
//$users = $db;
//$user = $db->findOne(['email' => 'Ivanov@mail.ru']);
////                $department = $depart->findOne(['department_code' => '04']);
////                $ref = $db->updateOne($user, ['$set' => ['department' => $department]]);

}