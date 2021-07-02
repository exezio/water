<?php


namespace Core\lib;


trait HelperFirstLetterOfArrayToUpperCase
{

    public function firstLetterToUpperCase($array): array
    {
        return array_map(function ($value) {
            return ucfirst($value);
        }, $array);
    }

}