<?php


namespace App\Models;


use Browser;
use Core\Model;
use Core\lib\HelperGenerateAuthData;
use Core\Router;


class RefreshToken extends Model
{
    use HelperGenerateAuthData;

    public function refreshToken(): bool|array
    {

        $headers = getallheaders();
        if (isset($headers['Refresh_token'], $headers['Sign'])){
            $browserLib = new Browser();
            $platform = $browserLib->getPlatform();
            $userSession = $this->usersSessionsCollection->findOne(
                ["{$platform}.refresh_token" => $headers['Refresh_token']],
                [
                    'projection' => [
                        $platform => 1,
                        '_id' => 1
                    ]
                ]
            );
            if(isset($userSession)){
                $refreshTokenClient = $headers['Refresh_token'];
                $refreshToken = $userSession[$platform]['refresh_token'];
                $signClient = $headers['Sign'];
                $sign = hash_hmac('sha256', $userSession[$platform]['secret'] . Router::getUrl(), $userSession[$platform]['secret']);
                debug($sign);
                if(hash_equals($refreshTokenClient, $refreshToken) && hash_equals($signClient, $sign)){
                    $sessionData = $this->generateSessionData();

                    $this->usersSessionsCollection->updateOne(
                        ['_id' => $userSession['_id']],
                        ['$set' => [$platform => $sessionData['sessionData']]]
                    );
                    return $sessionData['userSessionData'];
                }
            }
        }
            self::addError(code: 400, message: 'Ошибка обновления');
            return false;

    }

}