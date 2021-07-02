<?php


namespace App\Controllers;


use App\Models\Auth;
use App\Models\Security;
use Core\Controller;

class AuthenticationController extends Controller
{

    public function loginAction(): void
    {
        if(Security::checkBan()){
            $login = new Auth();
            $login->checkLogin() ? sendResponse(code: 200) : $login::getError();
        }else Security::getError();

    }

    public function createPasswordAction(): void
    {
        if(Security::checkBan()){
            $createPassword = new Auth();
            $createPassword->createPassword() ? sendResponse(code: 200) : $createPassword::getError();
        }else Security::getError();
    }

    public function authAction(): void
    {
        if(Security::checkBan()){
            $auth = new Auth();
            $authData = $auth->auth();
            $authData ? sendResponse(code: 200, data: $authData) : $auth::getError();
        }else Security::getError();
    }

}