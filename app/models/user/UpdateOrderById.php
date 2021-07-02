<?php


namespace App\Models\User;


use App\Models\Calendar;
use App\Models\Order;
use Core\lib\Registry;
use MongoDB\BSON\ObjectId;

class UpdateOrderById extends Order
{

    private array $attributesPutPermission = [
        'id' => '',
        'delivery_place_code' => '',
        'delivery_date' => '',
        'cabinet' => '',
        'responsible' => '',
        'note' => '',
        'order' => [],
    ];

    private array $rulesPutPermission = [
        'required' => [
            ['id']
        ],
        'validateMongoId' => ['id']
    ];

    public function updateOrder(): bool
    {
        $this->loadAttributes($this->inputData, $this->attributesPutPermission);
        if ($this->validate($this->attributesPutPermission, $this->rulesPutPermission)) {
            $user = Registry::get('user');
            $order = (array)$this->ordersCollection->findOne([
                '_id' => new ObjectId($this->attributesPutPermission['id']),
                'user.login' => $user['login']
            ]);
            if ($order) {
                if (strtotime($order['delivery_date']) >= strtotime('now')) {
                    $department = $this->departmentsCollection->findOne(['code' => $user['department']['code']]);
                    $clearedInputData = $this->clearingAttributes(attributes: $this->attributesPutPermission);
                    $adaptedArray = $this->arrayAdaptation(
                        clearedInputData: $clearedInputData,
                        order: $order,
                        department: $department
                    );
                    if ($adaptedArray) {
                        $this->ordersCollection->findOneAndUpdate($order, ['$set' => $adaptedArray]);
                        isset($adaptedArray['delivery_date']) ?
                            $this->adjustmentOfOrdersAfterChange(department: $department, order: $order, deliveryDate: $adaptedArray['delivery_date']) :
                            $this->adjustmentOfOrdersAfterChange(department: $department, order: $order);
                        return true;
                    } else {
                        self::addError(code: 400, message: 'Проверьте введенные данные');
                        return false;
                    }
                } else {
                    self::addError(code: 400, message: 'Ошибка редактирования. Заказ уже выполнен');
                    return false;
                }
            }
        }
        self::addError(code: 400, message: 'Заказ не найден');
        return false;
    }

    private function clearingAttributes($attributes)
    {
        foreach ($attributes as $item => $value) {
            if (!$value || $item === 'id') {
                unset($attributes[$item]);
            }
        }
        return $attributes;
    }

    private function arrayAdaptation($clearedInputData, $order, $department): bool|array
    {

        $adaptedArray = [];

        if (isset($clearedInputData['delivery_place_code'])) {
            $deliveryPlace = isset($department['delivery_places'][$clearedInputData['delivery_place_code']]['name']) ?
                $department['delivery_places'][$clearedInputData['delivery_place_code']]['name'] : null;
            if (isset($deliveryPlace)) {
                $adaptedArray['delivery_place'] = $deliveryPlace;
            } else {
                self::addError(code: 400, message: 'Неверно указано место доставки');
                return false;
            }
        }

        if (isset($clearedInputData['cabinet'])) {
            $adaptedArray['cabinet'] = $clearedInputData['cabinet'];
        }

        if (isset($clearedInputData['responsible'])) {
            $adaptedArray['responsible'] = $clearedInputData['responsible'];
        }

        if (isset($clearedInputData['note'])) {
            $adaptedArray['note'] = $clearedInputData['note'];
        }

        if (isset($clearedInputData['order']) || isset($clearedInputData['delivery_date'])) {
            $deliveryDate = isset($clearedInputData['delivery_date']) ? $clearedInputData['delivery_date'] : $order['delivery_date'];

            $deliveryPlaceCode = isset($clearedInputData['delivery_place_code']) ?
                $clearedInputData['delivery_place_code'] :
                $this->getDeliveryPlaceCode(
                    deliveryPlaceName: (string)$order['delivery_place'],
                    departmentPlaces: (array)$department['delivery_places']
                );
            if (!Calendar::checkDeliveryDate(
                dateDelivery: $deliveryDate,
                departmentCode: $department['code'],
                deliveryPlaceCode: $deliveryPlaceCode
            )) {
                return false;
            }

            $countLiters = isset($clearedInputData['order']) ?
                $this->getOrderedLitters(orders: $clearedInputData['order']) :
                $this->getOrderedLitters(orders: (array)$order['order']);
            if (!$countLiters) {
                return false;
            }

            $checkBalance = isset($clearedInputData['order']) ?
                $this->balanceRecalculating(
                    liters: $countLiters,
                    deliveryDate: $deliveryDate,
                    department: $department,
                    order: $order,
                ) :
                $this->balanceRecalculating(
                    liters: $countLiters,
                    deliveryDate: $deliveryDate,
                    department: $department,
                    order: $clearedInputData['order']
                );

            if (!$checkBalance) {
                return false;
            }

            if (isset($clearedInputData['delivery_date'])) {
                $adaptedArray['delivery_date'] = date('d.m.Y', strtotime($deliveryDate));
                $adaptedArray['delivery_date_timestamp'] = strtotime($deliveryDate);
            }

            if (isset($clearedInputData['order'])) {
                $adaptedArray['order'] = $clearedInputData['order'];
                $adaptedArray['liters'] = $countLiters;
//                $adaptedArray['balance_after'] = $this->getBalanceAfterUpdate(
//                    liters: $countLiters,
//                    deliveryDate: $deliveryDate,
//                    order: $order
//                );

            }

        }

        if (empty($adaptedArray)) {
            return false;
        }
        $adaptedArray['last_update'] = date('d.m.Y H:i:s', strtotime('now'));
        return $adaptedArray;

    }

//    private function getDeliveryPlaceCode($deliveryPlaceName, $departmentPlaces): string
//    {
//        foreach ($departmentPlaces as $placeCode => $values) {
//            if ($values['name'] == $deliveryPlaceName) {
//                return $placeCode;
//            }
//        }
//    }

//    private function balanceRecalculating($liters, $order, $deliveryDate, $department): bool
//    {
//        $allDepartmentsOrdersPerMonth = $this->ordersCollection->find(          //Только за текущий месяц
//            [
//                'department_code' => $department['code'],
//                'delivery_date_timestamp' => [
//                    '$gte' => strtotime("first day of {$deliveryDate}"),
//                    '$lte' => strtotime("last day of {$deliveryDate}")
//                ],
//                '_id' => [
//                    '$ne' => $order['_id']
//                ]
//            ]
//        )->toArray();
////        $department = $this->departmentsCollection->findOne(['_id' => new ObjectId($order['user']['department']['_id'])]);
////        Registry::set('department', $department);
//        $countOrderedLiters = $this->getSpentLimit($allDepartmentsOrdersPerMonth);
//        if ($department['water_limit'] < $countOrderedLiters + $liters) {
//            self::addError(
//                code: 400,
//                message:
//                "Лимит отдела превышен, текущий остаток на " . date('m.Y', $order['delivery_date_timestamp']) . ' - ' .
//                $department['water_limit'] - $countOrderedLiters . " литров");
//            return false;
//        }
//        return true;
//    }

//    private function getBalanceAfterUpdate($liters, $deliveryDate, $order): int
//    {
//        $department = Registry::get('department');
//        $previousOrders = $this->ordersCollection->find([
//            'department_code' => $order['department_code'],
//            'order_date_timestamp' => [
//                '$lte' => $order['order_date_timestamp']
//            ],
//            'delivery_date_timestamp' => [
//                '$gte' => strtotime("first day of {$deliveryDate}"),
//                '$lte' => strtotime("last day of {$deliveryDate}")
//            ],
//            '_id' => [
//                '$ne' => $order['_id']
//            ]
//        ], [
//            'sort' => ['order_date_timestamp' => 1]
//        ])->toArray();
//        $countLitersOfPrevOrders = $this->getSpentLimit($previousOrders);
//        return $department['water_limit'] - $countLitersOfPrevOrders - $liters;
//    }

//    private function adjustmentOfOrdersAfterChange($department, $order, $deliveryDate = null)
//    {
//        $currentBalance = $department['water_limit'];
//        $deliveryDate = $deliveryDate ? : $order['delivery_date'];
//        $subsequentOrders = $this->ordersCollection->find(
//            [
//                'department_code' => $department['code'],
//                'delivery_date_timestamp' => [
//                    '$gte' => strtotime("first day of {$deliveryDate}"),
//                    '$lte' => strtotime("last day of {$deliveryDate}")
//                ]
//            ],
//            [
//                'sort' => ['order_date_timestamp' => 1]
//            ]
//        );
//
//        foreach ($subsequentOrders as $order) {
//            $currentBalance -= $order['liters'];
//            $this->ordersCollection->updateOne(
//                [
//                    '_id' => new ObjectId($order['_id'])
//                ],
//                [
//                    '$set' => ['balance_after' => $currentBalance]
//                ]);
//        }
//    }

}