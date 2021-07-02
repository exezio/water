<?php


namespace App\Models;


use Browser;
use Core\lib\DataBase;
use Core\lib\Registry;
use Core\Model;
use Core\Router;

class CheckAuth extends Model
{

    public static function checkAuth(): bool
    {
        $headers = getallheaders();
        if (Security::checkBan() && isset($headers['Authorization'], $headers['Sign'])) {

            $browserLib = new Browser();
            $platform = $browserLib->getPlatform();
            $token_client = self::getUserToken();
            $sign_client = $headers['Sign'];
            $client = DataBase::instance()->getClient();
            $userSessionsCollection = $client->water->users_sessions;
            $userSession = $userSessionsCollection->findOne(
                ["{$platform}.access_token" => $token_client],
                [
                    'projection' =>
                        [
                            $platform => 1,
                            'user_login' => 1,
                            'user_id' => 1
                        ]
                ]);
            if ($userSession) {
                if($userSession[$platform]['expires'] < strtotime('now')){
                    self::addError(code: 400, message: 'Токен авторизации просрочен');
                    return false;
                }
                $userCollection = $client->water->users;
                $user = $userCollection->findOne(['_id' => $userSession['user_id']]);
                $token = $userSession[$platform]['access_token'];
                $sign = hash_hmac('sha256', $userSession[$platform]['secret'] . Router::getUrl(), $userSession[$platform]['secret']);
                debug($sign);
                if (hash_equals($sign, $sign_client) && hash_equals($token, $token_client)) {
                    Registry::set('user', $user);
                    return true;
                } else {
                    self::addError(500, 'Неизвестная ошибка, аутентифицируйтесь заново');
                    return false;
                }
            }
        }
        self::addError(401, 'Необходимо пройти аутентификацию');
        return false;
    }

    private static function getUserToken(): string
    {
        $headers = getallheaders();
        return trim(str_replace('Bearer', '', $headers['Authorization']));
    }

}