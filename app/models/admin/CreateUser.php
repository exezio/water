<?php


namespace App\Models\Admin;


use Core\Model;
use MongoDB\Exception\Exception;


class CreateUser extends Model
{

    private array $attributesCreateUser = [
        'user_name' => '',
        'login' => '',
        'phone' => '',
        'department_code' => '',
        'role' => ''
    ];

    private array $rulesCreateUser = [
        'required' => [
            ['user_name'],
            ['login'],
            ['phone'],
            ['department_code'],
            ['role']
        ],
        'checkRoles' => ['role'],
        'email' => [
            ['login']
        ]
    ];

    public function createUser()
    {
        $this->loadAttributes($this->inputData, $this->attributesCreateUser);
        if($this->validate($this->attributesCreateUser, $this->rulesCreateUser)){
            $department = $this->departmentsCollection->findOne(['code' => (int) $this->attributesCreateUser['department_code']]);
            $user = $this->usersCollection->findOne(
                ['login' => $this->attributesCreateUser['login'],],
                ['collation' => ['locale' => 'en', 'strength' => 1]]
            );
            if($user){
                self::addError(400, "Пользователь {$user['user_name']} уже существует" );
                return false;
            }
            if($department){
                $query = [
                    'user_name' => $this->inputData['user_name'],
                    'login' => $this->inputData['login'],
                    'phone' => $this->inputData['phone'],
                    'role' => $this->attributesCreateUser['role'],
                    'department' => $department
                    ];
                try {
                    $this->usersCollection->insertOne($query);
                    return true;
                }catch (Exception){
                    self::addError(code: 400, message: "Не удается добавить пользователя {$this->inputData['user_name']}");

                }
            }else self::addError(code: 401, message: 'Не удается найти соответствующее подразделение');

        } else self::addError(code: 401, message: 'Проверьте введенные данные');
    }


}