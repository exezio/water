<?php


namespace App\Models\User;


use App\Models\Order;
use Core\lib\HelperCreateArrayOfOrders;
use Core\lib\Registry;
use MongoDB\BSON\ObjectId;

class getAllOrdersByDate extends Order
{
    use HelperCreateArrayOfOrders;

    private array $attributesGetAllOrdersBySelectedMonth = [
        'date_start' => '',
        'date_end' => ''
    ];

    private array $rulesGetOrderById = [
        'required' => [
            ['date_start']
        ],
        'validateDate' => [['date_end'], ['date_start']]
    ];

    public function getAllOrdersByDate(): bool|array
    {
        $this->loadAttributes($this->inputData, $this->attributesGetAllOrdersBySelectedMonth);
        if ($this->validate($this->attributesGetAllOrdersBySelectedMonth, $this->rulesGetOrderById)) {
            $dateStart = $this->attributesGetAllOrdersBySelectedMonth['date_start'];
            $dateEnd = $this->attributesGetAllOrdersBySelectedMonth['date_end'];
            $user = Registry::get('user');
            $department = $this->departmentsCollection->findOne(['_id' => new ObjectId($user['department']['_id'])]);
            if ($dateEnd) {

                if(strtotime($dateStart) < strtotime($dateEnd)){
                    $orders = $this->ordersCollection->find([
                        'department_code' => $department['code'],
                        'delivery_date_timestamp' => [
                            '$gte' => strtotime($this->attributesGetAllOrdersBySelectedMonth['date_start']),
                            '$lte' => strtotime($this->attributesGetAllOrdersBySelectedMonth['date_end'])
                        ]
                    ])->toArray();
                }else{
                    self::addError(code: 400, message: 'Проверьте введенные даты');
                    return false;
                }

            }else{
                $orders = $this->ordersCollection->find([
                    'department_code' => $department['code'],
                    'delivery_date_timestamp' => strtotime($dateStart)
                ])->toArray();
            }

            if(!$orders){
                self::addError(code: 400, message: 'Заказы за указанную дату не найдены');
                return false;
            }

            return $this->createArrayOfOrders($orders);
        }
        self::addError(code: 400, message: 'Проверьте введенные данные');
        return false;
    }

    private function validateDateRange($dateStart, $dateEnd = null)
    {
        if ($dateEnd) {
            if (strtotime($dateStart) < strtotime($dateEnd)) return true;
            return false;
        }
        return true;
    }

}