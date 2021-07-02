<?php


namespace App\Models\User;


use App\Models\Order;
use Core\lib\Registry;
use MongoDB\BSON\ObjectId;

class DeleteOrderById extends Order
{

    private array $attributesDeleteOrderById = [
        'id' => ''
    ];

    private array $rulesDeleteOrderById = [
        'required' => [
            ['id']
        ],
        'validateMongoId' => ['id']
    ];


    public function deleteOrderById()
    {

        $this->loadAttributes($this->inputData, $this->attributesDeleteOrderById);
        if($this->validate($this->attributesDeleteOrderById, $this->rulesDeleteOrderById)){
            $user = Registry::get('user');
            $order = (array) $this->ordersCollection->findOne([
                    '_id' => new ObjectId($this->attributesDeleteOrderById['id']),
                    "user.login" => $user['login']
                ]);
            if($order){
                $department = $this->departmentsCollection->findOne(['code' => $order['department_code']]);
                $this->ordersCollection->deleteOne(['_id' => new ObjectId($this->attributesDeleteOrderById['id'])]);
                $this->adjustmentOfOrdersAfterChange(department: $department, order: $order);
                return true;
            }
            self::addError(code: 400, message: 'Заказ не найден');
            return false;
        }
        self::addError(code: 400, message: 'Проверьте введенные данные');
        return false;

    }

}