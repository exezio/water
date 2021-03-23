<?php


namespace App\Models;


use Core\Model;

class Create extends Model
{
    private array $attributesOrder = [
        'order' => [],
        'delivery_place' => ''
    ];

    private array $rulesOrder = [
        'required' => [
            ['order'],
            ['delivery_place']
        ]
    ];

    public function create()
    {
        $inputData = $this->inputData;
        $this->loadAttributes($inputData, $this->attributesOrder);
        if($this->validate($this->attributesOrder, $this->rulesOrder))
        {
            $dbUsers = $this->mongoClient->selectCollection(DB_NAME, DB_COLLECTION_USERS);
            $dbDepartments = $this->mongoClient->selectCollection(DB_NAME, DB_COLLECTION_DEPARTMENT);
            $department = $dbDepartments->findOne(['department_code' => "1"]);
            $userToken = self::getUserToken();

            $query = [
                'user_name' => 'Голубев Дмитрий Сергеевич',
                'email' => "Ivan@gmail.com",
                'phone' => "8 985 345 57 78",
                'password' => '',
                'department' => $department
            ];
        } else self::addError(code: 401, message: "Не все данные");

    }

}