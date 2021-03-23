<?php


namespace App\Controllers;


use App\Models\CheckAuth;
use App\Models\Create;
use App\Models\Order;
use Core\Controller;

class UserController extends Controller
{

    //ПЕРЕДЕЛАТЬ ЗАГРУЗКУ АТТРИБУТОВ (ВЫНЕСТИ ЛОГИКУ В КОНСТРУКТОР МОДЕЛИ). ТАК ЖЕ ВАЛИДАЦИЯ
    public function createAction()
    {
        if(CheckAuth::checkAuth()) {
            $createOrder = new Create();
            $createOrder->create();
        }else CheckAuth::getError();
    }

    public function getAllAction()
    {
        echo "getAll A";

        $order = new Order();

    }

    public function getByIdAction()
    {
        echo "getById A";
    }

    public function updateAction()
    {
        echo "update A";
    }

    public function deleteAction()
    {
        echo "delete A";
    }

}