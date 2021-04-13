<?php


namespace App\Models;


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
        'email' => [
            ['login']
        ]
    ];

    public function createUser()
    {
        $inputData = $this->inputData;
        $this->loadAttributes($inputData, $this->attributesCreateUser);
        if($this->validate($this->attributesCreateUser, $this->rulesCreateUser)){
            $department = $this->departmentsCollection->findOne(['code' => (int) $inputData['department_code']]);
            if($department){
                $query = [
                    'user_name' => $inputData['user_name'],
                    'login' => $inputData['login'],
                    'phone' => $inputData['phone'],
                    'department' => $department,
                    'role' => $inputData['role']
                ];
            }else self::addError(401, 'Не удается найти соответствующее подразделение');
            try {
                $this->usersCollection->insertOne($query);
            }catch (Exception){
                self::addError(code: 400, message: "Не удается добавить пользователя {$inputData['user_name']}");

            }


        } else self::addError(code: 401, message: 'Проверьте введенные данные');

    }

}