<?php


namespace App\Controllers\admin;


use App\Models\CheckAuth;
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
            if(CheckAuth::checkAuth()){
                $users = new CreateUser();
                $users->createUser() ? sendResponse(200) : $users::getError();
            }else CheckAuth::getError();
    }

}