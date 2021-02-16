<?php


namespace App\Controllers;


use App\Models\Order;
use Core\Controller;

class UserController extends Controller
{

    public function createAction()
    {
        echo "create A";
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