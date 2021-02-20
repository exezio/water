<?php


namespace App\Controllers\admin;


use App\Models\CreateUser;
use Core\Controller;

class AdminController  extends Controller
{

    public function getAllAction()
    {
        echo "ADMIN CONTROLLER";
    }

    public function createUserAction()
    {
        $users = new CreateUser();
    }

}