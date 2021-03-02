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
            $data = $login->filterInput();
            $login->loadAttributesLogin($data);
            $login->checkLogin();
    }

    public function createPasswordAction()
    {
        if(!empty($_POST))
        {
            $auth = new Auth();
            $data = $auth->filterInput();
            $auth->loadAttributesCreatePassword($data);
            $auth->createPassword();
        }
    }

    public function authAction(): void
    {
        if(!empty($_POST))
        {
            $auth = new Auth();
            $data = $auth->filterInput();
            $auth->loadAttributesAuth($data);
            $auth->auth();

        }
    }





}