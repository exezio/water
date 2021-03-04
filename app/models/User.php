<?php


namespace App\Models;


use Core\Model;


class User extends Model
{

    public function filterInput()
    {
        $data = json_decode(file_get_contents('php://input'), JSON_FORCE_OBJECT);
        $args = array(
            'login' => FILTER_VALIDATE_EMAIL,
            'password' => FILTER_SANITIZE_SPECIAL_CHARS,
            'password_confirm' => FILTER_SANITIZE_SPECIAL_CHARS,
            'key' => FILTER_VALIDATE_INT,
            'token' => FILTER_SANITIZE_SPECIAL_CHARS,
            'sign' => FILTER_SANITIZE_SPECIAL_CHARS
        );
        return filter_var_array($data, $args);
    }


}