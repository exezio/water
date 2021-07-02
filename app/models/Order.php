<?php


namespace App\Models;
use Core\lib\HelperOrderLimit;
use Core\Model;
use MongoDB\BSON\ObjectId;

class Order extends Model
{
    use HelperOrderLimit;

    protected function getDeliveryPlaceCode($deliveryPlaceName, $departmentPlaces): string
    {
        foreach ($departmentPlaces as $placeCode => $values) {
            if ($values['name'] == $deliveryPlaceName) {
                return $placeCode;
            }
        }
    }

    protected function balanceRecalculating($liters, $deliveryDate, $department, $order = null): bool
    {
        $allDepartmentsOrdersPerMonth = $order ?
            $this->ordersCollection->find(          //Только за текущий месяц
            [
                'department_code' => $department['code'],
                'delivery_date_timestamp' => [
                    '$gte' => strtotime("first day of {$deliveryDate}"),
                    '$lte' => strtotime("last day of {$deliveryDate}")
                ],
                '_id' => [
                    '$ne' => $order['_id']
                ]
            ]
        )->toArray() :
            $this->ordersCollection->find(          //Только за текущий месяц
            [
                'department_code' => $department['code'],
                'delivery_date_timestamp' => [
                    '$gte' => strtotime("first day of {$deliveryDate}"),
                    '$lte' => strtotime("last day of {$deliveryDate}")
                ]
            ]
        )->toArray();
        $countOrderedLiters = $this->getSpentLimit($allDepartmentsOrdersPerMonth);
        if ($department['water_limit'] < $countOrderedLiters + $liters) {
            self::addError(
                code: 400,
                message: "Лимит отдела превышен, текущий остаток на " .
                date('m.Y', strtotime($deliveryDate)) . ' - ' .
                $department['water_limit'] - $countOrderedLiters . " литров");
            return false;
        }
        return true;
    }

    protected function adjustmentOfOrdersAfterChange($department, $order = null, $deliveryDate = null)
    {
        $currentBalance = $department['water_limit'];
        $deliveryDate = $deliveryDate ? : $order['delivery_date'];
        $subsequentOrders = $this->ordersCollection->find(
            [
                'department_code' => $department['code'],
                'delivery_date_timestamp' => [
                    '$gte' => strtotime("first day of {$deliveryDate}"),
                    '$lte' => strtotime("last day of {$deliveryDate}")
                ]
            ],
            [
                'sort' => ['order_date_timestamp' => 1]
            ]
        );

        foreach ($subsequentOrders as $order) {
            $currentBalance -= $order['liters'];
            $this->ordersCollection->updateOne(
                [
                    '_id' => new ObjectId($order['_id'])
                ],
                [
                    '$set' => ['balance_after' => $currentBalance]
                ]);
        }
    }
}