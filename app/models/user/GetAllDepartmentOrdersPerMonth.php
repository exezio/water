<?php


namespace App\Models\User;


use Core\lib\HelperCreateArrayOfOrders;
use Core\lib\Registry;
use Core\Model;

class GetAllDepartmentOrdersPerMonth extends Model
{

    use HelperCreateArrayOfOrders;

    public function getAllDepartmentOrdersPerMonth(): array|bool
    {
        $user = Registry::get('user');
        $orders = $this->ordersCollection->find(
            [
                'department_code' => $user['department']['code'],
                'delivery_date_timestamp' =>
                    [
                        '$gte' => strtotime('first day of this month'),
                        '$lte' => strtotime('last day of this month')
                    ]
            ],
            [
                'sort' => ['date' => 1]
            ])->toArray();
        if ($orders) {
            return $this->createArrayOfOrders(orders: $orders);
        }
        self::addError(400, 'На данный момент заказов за текущий месяц нет');
        return false;
    }

//    private function createArrayOfOrders($orders)
//    {
//        $arrayOfOrders = ['orders' => []];
//        foreach ($orders as $item => $order) {
//            array_push($arrayOfOrders['orders'], [
//                'id' => (string) $order['_id'],
//                'user' => $order['user']['user_name'],
//                'email' => $order['user']['login'],
//                'order' => $order['order'],
//                'delivery_place' => $order['delivery_place'],
//                'phone' => $order['user']['phone'],
//                'date' => $order['date'],
//                'delivery_date' => $order['delivery_date'],
//                'liters' => $order['liters'],
//                'balance_after' => $order['balance_after']
//            ]);
//        }
//        return $arrayOfOrders;
//    }

}