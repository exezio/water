<?php


namespace Core\lib;


trait HelperOrderLimit
{

    public function getSpentLimit($existingOrders)
    {
        return array_reduce($existingOrders, function ($carry, $item){
            $carry += $item['liters'];
            return $carry;
        });
    }

    public function getOrderedLitters($orders): int|null
    {
        return array_reduce($orders, function ($liters, $item){
            if(is_array($item) && count($item) === 2){
                match((int) $item[0]){
                    19, 5 => $liters += (int) $item[0] * $item[1],
                    default => null
                };
                return (int) $liters;
            }
            return null;
        });
    }


}