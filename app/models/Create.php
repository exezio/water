<?php


namespace App\Models;


use Core\Model;

class Create extends Model
{
    private array $attributesOrder = [
        'order' => [],
        'delivery_place_code' => '',
        'cabinet' => '',
        'responsible' => ''
    ];

    private array $rulesOrder = [
        'required' => [
            ['order'],
            ['delivery_place_code']
        ]
    ];

    public function create(): bool
    {
        $inputData = $this->inputData;
        $this->loadAttributes($inputData, $this->attributesOrder);
        if($this->validate($this->attributesOrder, $this->rulesOrder))
        {
            $dump = new DumpD();
            $dump->DumpD();
            //СОЗДАТЬ СВОЙСТВА КЛАССА ПОДКОЛЮЧЕНИЯ К БД КОЛЛЕКЦИЙ
            $userToken = self::getUserToken();
            $user = $this->usersCollection->findOne(['token' => $userToken]);
            $deliveryPlaces = $user['department']['delivery_places'];
            if(isset($deliveryPlaces[$this->attributesOrder['delivery_place_code']])){
                $deliveryPlace = $deliveryPlaces[$this->attributesOrder['delivery_place_code']];
                $query = [
                    'user' => $user,
                    'delivery_place' => $deliveryPlace['name'],
                    'order' => $this->attributesOrder['order'],
                    'phone' => $deliveryPlace['phone']
                ];
                $this->ordersCollection->insertOne($query);
                return true;
            } else self::addError(code: 401, message: 'Проверьте место доставки');
        } else self::addError(code: 401, message: "Не все данные");
        return false;
    }
}