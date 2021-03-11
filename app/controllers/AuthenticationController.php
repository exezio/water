<?php


namespace App\Controllers;


use App\Models\Auth;
use Core\Controller;

class AuthenticationController extends Controller
{

    public function loginAction(): void
    {
            $login = new Auth();
            $login->checkLogin() ? sendResponse(code: 200) : $login::getError();
    }

    public function createPasswordAction(): void
    {
            $createPassword = new Auth();
            $createPassword->createPassword() ? sendResponse(code: 200) : $createPassword::getError();
    }

    public function authAction(): void
    {
            $auth = new Auth();
            $authData = $auth->auth();
            $authData ? sendResponse(code: 200, data: $authData) : $auth::getError();
    }

}