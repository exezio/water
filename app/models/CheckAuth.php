<?php


namespace App\Models;


use Core\lib\DataBase;
use Core\Model;
use Core\Router;
use MongoDB\Client;

class CheckAuth extends Model
{

    public static function checkAuth(): bool
    {
        $headers = getallheaders();
        if (isset($headers['Authorization'], $headers['sign'])) {
            $db = DataBase::instance()->getClient();
            $mongoClient = $db->selectCollection(DB_NAME, DB_COLLECTION_USERS);

            $token_client = self::getUserToken();
            $sign_client = $headers['sign'];

            $user = $mongoClient->findOne(['token' => $token_client]);
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
                self::addError(401, 'Какое-то сообщение');
                return false;
            }
        } else {
            self::addError(401, 'Необходимо авторизоваться');
            return false;
        }
    }

}