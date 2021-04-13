<?php


namespace App\Models;


use Core\lib\DataBase;
use Core\lib\Registry;
use Core\Model;
use Core\Router;

class CheckAuth extends Model
{

    public static function checkAuth(): bool
    {
        $headers = getallheaders();
        if (isset($headers['Authorization'], $headers['sign'])) {
            $db = DataBase::instance()->getClient();
            $usersCollection = $db->water->users;
            $token_client = self::getUserToken();
            $sign_client = $headers['sign'];
            $user = $usersCollection->findOne(['token' => $token_client]);
            Registry::set('user', $user);
            if ($user) {
                $token = $user['token'];
                $sign = hash_hmac('sha256', $user['secret'] . Router::getUrl(), $user['secret']);
                if (hash_equals($sign, $sign_client) && hash_equals($token, $token_client)) {
                    return true;
                } else {
                    self::addError(500, 'Неизвестная ошибка, авторизируйтесь заново');
                    return false;
                }
            } else {
                self::addError(401, 'Попытка подделать токен');
                return false;
            }
        } else {
            self::addError(401, 'Необходимо авторизоваться');
            return false;
        }
    }

}