<?php


namespace App\Controllers;


use App\Models\CheckAuth;
use App\Models\Create;
use App\Models\Order;
use Core\Controller;

class UserController extends Controller
{

    public function createAction()
    {
        if(CheckAuth::checkAuth()) {
            $createOrder = new Create();
            $createOrder->create() ? sendResponse(code: 200) : $createOrder::getError();
        }else CheckAuth::getError();
    }

    public function getAllAction()
    {
        echo "getAll A";
        if(CheckAuth::checkAuth()){

        }else CheckAuth::getError();
//        $order = new Order();

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