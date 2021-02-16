<?php


namespace App\Controllers;


use App\Models\Auth;
use App\Models\Calendar;
use App\Models\Order;
use Core\Controller;
use Core\lib\DataBase;

class UserController extends Controller
{

    protected string $token = '';

    /**
     * Processing an authorization attempt
     * @return void
     */
    public function loginAction() : void
    {
        if(!empty($_POST))
        {
            $login = new Auth();
            $data = $_POST;
            $login->loadAttributes(data: $data, subject: 'attributesCheckLogin');
            $login->checkLogin();

        }
        else{
            http_response_code(400);
        }
    }

    public function authAction(): void
    {
        if(!empty($_POST))
        {
            $auth = new Auth();
            $data = $_POST;
            $auth->auth();
        }else{
            http_response_code(400);
        }
    }

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

    public function calendarAction()
    {
        $calendar = new Calendar();
        $calendar->getHolidayDates();
    }
}