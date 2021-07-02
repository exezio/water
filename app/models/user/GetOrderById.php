<?php


namespace App\Models\User;

use Core\lib\HelperCreateArrayOfOrders;
use Core\lib\Registry;
use Core\Model;
use MongoDB\BSON\ObjectId;


class GetOrderById extends Model
{

    use HelperCreateArrayOfOrders;

    private array $attributesGetOrderById = [
        'id' => '',
    ];

    private array $rulesGetOrderById = [
        'required' => [
            ['id'],
        ],
    ];

    public function getOrderById(): bool|array
    {
        $this->loadAttributes($this->inputData, $this->attributesGetOrderById);
        if ($this->validate($this->attributesGetOrderById, $this->rulesGetOrderById)) {
            $user = Registry::get('user');
            $order = $this->ordersCollection->findOne(['_id' => new ObjectId($this->attributesGetOrderById['id']), 'user' => $user]);
            if ($order) {
                return $this->createArrayOfOrder((array) $order);
            }
        }
        self::addError(code: 400, message: 'Заказ не найден');
        return false;
    }

}