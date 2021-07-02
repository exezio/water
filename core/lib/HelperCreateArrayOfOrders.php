<?php


namespace Core\lib;


trait HelperCreateArrayOfOrders
{

    public function createArrayOfOrders($orders)
    {

        $arrayOfOrders = ['orders' => []];
        foreach ($orders as $item => $order) {
                array_push($arrayOfOrders['orders'], [
                    $this->getOrderTemplate($order)
                ]);
        }

        return $arrayOfOrders;
    }

    public function createArrayOfOrder($order)
    {
        $arrayOfOrder = ['order' => []];
        array_push($arrayOfOrder['order'], [
           $this->getOrderTemplate($order)
        ]);
        return $arrayOfOrder;
    }

    public function getOrderTemplate($order): array
    {
        return [
            'id' => (string)$order['_id'],
            'user' => $order['user']['user_name'],
            'email' => $order['user']['login'],
            'order' => $order['order'],
            'delivery_place' => $order['delivery_place'],
            'phone' => $order['user']['phone'],
            'date' => $order['date'],
            'delivery_date' => $order['delivery_date'],
            'liters' => $order['liters'],
            'balance_after' => $order['balance_after']
        ];
    }

    public function getOrderInfoTemplate($order): array
    {

    }



}