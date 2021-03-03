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
            $data ? $login->loadAttributesLogin($data) : sendResponse(400);
            $login->checkLogin();
    }

    public function createPasswordAction()
    {
        if(!empty($_POST))
        {
            $auth = new Auth();
            $data = $auth->filterInput();
            $data ? $auth->loadAttributesCreatePassword($data) : sendResponse(400);
            $auth->createPassword();
        }
    }

    public function authAction(): void
    {
        if(!empty($_POST))
        {
            $auth = new Auth();
            $data = $auth->filterInput();
            $data ? $auth->loadAttributesAuth($data) : sendResponse(400);
            $auth->auth();

        }
    }





}