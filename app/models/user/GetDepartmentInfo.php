<?php


namespace App\Models\User;


use App\Models\Calendar;
use Core\lib\HelperCreateArrayOfOrders;
use Core\lib\HelperOrderLimit;
use Core\lib\Registry;
use Core\Model;

class GetDepartmentInfo extends Model
{

    use HelperOrderLimit;
    use HelperCreateArrayOfOrders;

    public function getDepartmentInfo()
    {
        $user = Registry::get('user');
        $department = $this->departmentsCollection->findOne(['code' => $user['department']['code']]);
        $currentMonth = date('d.m.Y', strtotime('now'));
        $nextMonth = date('d.m.Y', strtotime('first day of next month'));
        $balanceAndOrdersForCurrentMonth = $this->getBalanceAndOrders(
            $department['code'],
            $department['water_limit'],
            $currentMonth
        );
        $balanceAndOrdersForNextMonth = $this->getBalanceAndOrders(
            $department['code'],
            $department['water_limit'],
            $nextMonth
        );
        $monthList = Calendar::getMonthsList();
        $ordersForToday = $this->getOrdersForToday($department['code']);
        return [
            'name' => $department['name'],
            'delivery_allowed' => $department['delivery_allowed'],
            'water_filter' => $department['water_filter'],
            'current_orders' => [
                $monthList[date('n')] => $balanceAndOrdersForCurrentMonth['orders'],
                $monthList[date('n', strtotime('first day of next month'))] => $balanceAndOrdersForNextMonth['orders']
            ],
            'current_water_limit' => [
                $monthList[date('n')] => [
                    'l' => $balanceAndOrdersForCurrentMonth['remaining_liters'],
                    'b' => floor($balanceAndOrdersForCurrentMonth['remaining_liters'] / 19),
                ],
                $monthList[date('n', strtotime('first day of next month'))] => [
                    'l' => $balanceAndOrdersForNextMonth['remaining_liters'],
                    'b' => floor($balanceAndOrdersForNextMonth['remaining_liters'] / 19)
                ]
            ],
            'delivery_today' => $ordersForToday
        ];

    }

    private function getBalanceAndOrders($departmentCode, $waterLimit, $date)
    {
        $data = [];
        $orders = $this->ordersCollection->find(
            [
                'department_code' => $departmentCode,
                'delivery_date_timestamp' => [
                    '$gte' => strtotime("first day of {$date}"),
                    '$lte' => strtotime("last day of {$date}")
                ]
            ],
            [
                'sort' => ['delivery_date_timestamp' => 1]
            ]
        )->toArray();
        $remainingLiters = $waterLimit - $this->getSpentLimit($orders);
        $data['orders'] = $this->createArrayOfOrders($orders) ? : 'Заказов пока что нет';
        $data['remaining_liters'] = $remainingLiters;
        return $data;
    }

    private function getOrdersForToday($departmentCode)
    {

        $orders = $this->ordersCollection->find([
            'department_code' => $departmentCode,
            'delivery_date_timestamp' => strtotime('now')
        ])->toArray();
        return $this->createArrayOfOrders($orders);
    }



}