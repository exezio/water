<?php


namespace App\Models;


use Core\Model;
use Core\Router;

class CheckAuth extends Model
{

    public static function checkAuth()
    {
        $headers = getallheaders();
        if (isset($headers['Authorization'], $headers['sign'])) {
            $db = self::$mongoClient->selectCollection($_ENV['DB_NAME'], $_ENV["DB_COLLECTION_USERS"]);
            $token_client = trim(str_replace('Bearer', '', $headers['Authorization']));
            $sign_client = $headers['sign'];

            $user = $db->findOne(['token' => $token_client]);

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
            self::addError(401, 'Необходимо авторизация');
            return false;
        }
    }

}