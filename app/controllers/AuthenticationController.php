<?php


namespace App\Controllers;


use App\Models\Auth;
use Core\Controller;

class AuthenticationController extends Controller
{

    protected string $token = '';

    /**Processing an authorization attempt
     * @return void
     */
    public function loginAction(): void
    {
            $login = new Auth();
            $data = $_POST;
            $login->loadAttributesLogin($data);
            $login->checkLogin();
    }

    public function authAction(): void
    {
        if(!empty($_POST))
        {
            $auth = new Auth();
            $data = $_POST;
            $auth->loadAttributesAuth($data);
            $auth->auth();

        }
    }

    public function createPasswordAction()
    {
        if(!empty($_POST))
        {
            $auth = new Auth();
            $data = $_POST;
            $auth->loadAttributesCreatePassword($data);
            $auth->createPassword();
        }
    }



}