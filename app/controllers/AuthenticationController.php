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



}