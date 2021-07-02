<?php


namespace App\Models\User;

use App\Models\Calendar;
use App\Models\Order;
use Core\lib\HelperOrderLimit;
use Core\lib\Registry;


class CreateOrder extends Order
{

    use HelperOrderLimit;

    private array $attributesOrder = [
        'order' => [],
        'delivery_place_code' => '',
        'cabinet' => '',
        'responsible' => '',
        'phone' => '',
        'delivery_date' => '',
        'note' => ''
    ];

    private array $rulesOrder = [
        'required' => [
            ['order'],
            ['delivery_place_code'],
            ['delivery_date']
        ],
        'checkOrder' => ['order']
    ];

    public function create(): bool
    {
        $user = Registry::get('user');
        $this->loadAttributes($this->inputData, $this->attributesOrder);
        if (
            $this->validate($this->attributesOrder, $this->rulesOrder)
            && Calendar::checkDeliveryDate(
                dateDelivery: $this->attributesOrder['delivery_date'],
                departmentCode: $user['department']['code'],
                deliveryPlaceCode: $this->attributesOrder['delivery_place_code']
            )
        ) {

            $department = $this->departmentsCollection->findOne(['code' => $user['department']['code']]);

            if(!$department){
                self::addError(code: 400, message: 'Департамент не найден');
            }

            if ($department['water_filter']) {
                self::addError(code: 400, message: 'Доставка недоступна - установлен фильтр');
                return false;
            }

            if (!$department['delivery_allowed']) {
                self::addError(code: 400, message: "{$department['name']} - доставка недоступна");
                return false;
            }

            $deliveryPlaces = $department['delivery_places'];
            $deliveryPlace = isset($deliveryPlaces[$this->attributesOrder['delivery_place_code']]) ?
                $deliveryPlaces[$this->attributesOrder['delivery_place_code']] :
                null;
            $orderedLiters = $this->getOrderedLitters(orders: $this->attributesOrder['order']);

            if(!$orderedLiters){
                self::addError(code: 400, message: 'Проверьте заказ');
                return false;
            }

            if (!$deliveryPlace) {
                self::addError(code: 400, message: 'Проверьте место доставки');
                return false;
            }

//            $currentOrdersOfDepartment = $this->ordersCollection->find([
//                'delivery_date_timestamp' => [
//                    '$gte' => strtotime("first day of {$this->attributesOrder['delivery_date']}"),
//                    '$lte' => strtotime("last day of {$this->attributesOrder['delivery_date']}")
//                ]
//            ])->toArray();
//            $spentLimit = $this->getSpentLimit(existingOrders: $currentOrdersOfDepartment);
            $checkBalance = $this->balanceRecalculating(
                liters: $orderedLiters,
                deliveryDate: $this->attributesOrder['delivery_date'],
                department: $department
            );
            if ($checkBalance) {
                $phone = $this->attributesOrder['phone'] ? : $deliveryPlace['phone'];
                $query = [
                    'user' => $user,
                    'delivery_place' => $deliveryPlace['name'],
                    'department_code' => $user['department']['code'],
                    'order' => $this->attributesOrder['order'],
                    'phone' => $phone,
                    'date' => date('d-m-Y H:i:s'),
                    'order_date_timestamp' => strtotime('now'),
                    'delivery_date' => date('d.m.Y', strtotime($this->attributesOrder['delivery_date'])),
                    'delivery_date_timestamp' => strtotime($this->attributesOrder['delivery_date']),
                    'liters' => $orderedLiters,
                    'cabinet' => $this->attributesOrder['cabinet'],
                    'responsible' => $this->attributesOrder['responsible'],
                    'note' => $this->attributesOrder['note']
                ];
                $this->ordersCollection->insertOne($query);
                $this->adjustmentOfOrdersAfterChange(department: $department, deliveryDate: $query['delivery_date']);
                return true;
            } else return false;
        } else {
            self::addError(code: 401, message: "Проверьте введенные данные");
        }
        return false;
    }

}