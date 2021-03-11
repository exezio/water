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

        $checkAuth = new CheckAuth();
        if($checkAuth->checkAuth()){
            $createOrder = new Create();
        }else{
            $checkAuth->getError();
        }
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