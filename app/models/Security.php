<?php


namespace App\Models;


use Core\lib\DataBase;
use Core\Model;
use DateTime;
use function date;

class Security extends Model
{

    public static function addFailAuthAttemptions($action)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $client = DataBase::instance()->getClient();
        $bansCollection = $client->water->bans;
        $currentDate = date('Y-m-d H:i:s');
        $currentFailedAttempts = $bansCollection->findOne(['ip' => $ip]);
        if ($currentFailedAttempts) {
            if ($currentFailedAttempts['count'] < 3) {
                $bansCollection->updateOne(['ip' => $ip], ['$inc' => ['count' => 1]]);
            } else {
                $bansCollection->updateOne(
                    ['ip' => $ip],
                    [
                        '$set' => [
                            'ban_date' => $currentDate,
                            'unbanned_date' => date('Y-m-d H:i:s', strtotime($currentDate . $_ENV['BAN_TIME_AUTH'] .' minutes')),
                            'reason' => 'exceeded the number of login attempts'
                        ],
                        '$unset' => [
                            'last_date' => 1
                        ]
                    ]
                );
                self::addError(code: 400, message:
                    "Неверный {$action} - превышен лимит попыток, разблокировка через {$_ENV['BAN_TIME_AUTH']} минут");
            }
        } else {
            $bansCollection->insertOne([
                'ip' => $ip,
                'last_date' => $currentDate,
                'count' => 1
            ]);
        }
    }

    public static function clearFailAttemptions()
    {
        $client = DataBase::instance()->getClient();
        $client->water->bans->findOneAndDelete(['ip' => $_SERVER['REMOTE_ADDR']]);
    }


    public static function checkBan(): bool
    {
        $client = DataBase::instance()->getClient();
        $bansCollection = $client->water->bans;
        $currentFailedAttempts = $bansCollection->findOne(['ip' => $_SERVER['REMOTE_ADDR']]);
        if (isset($currentFailedAttempts['ban_date'])) {
            $date = new DateTime();
            $currentDate = date('Y-m-d H:i:s');
            $unbannedDate = $currentFailedAttempts['unbanned_date'];
            $format = $date::createFromFormat('Y-m-d H:i:s', $unbannedDate);
            $interval = $date->diff($format);
            if($currentDate >= $unbannedDate){
                $bansCollection->deleteOne(['ip' => $_SERVER['REMOTE_ADDR']]);
                return true;
            }
            self::addError(400, "Повторить можно будет через {$interval->i} минут {$interval->s} секунд");
            return false;
        }
        return true;
    }

    public static function ban($reason, $time, $dimension)
    {
        $clientIp = $_SERVER['REMOTE_ADDR'];
        $client = DataBase::instance()->getClient();
        $bansCollection = $client->water->bans;
        $currentDate = date('Y-m-d H:i:s');
        $isBanned = $bansCollection->findOne(['ip' => $clientIp]);
        if(Security::checkBan())
        {
            $bansCollection->insertOne([
                'ip' => $clientIp,
                'ban_date' => $currentDate,
                'unbanned_date' => date('Y-m-d H:i:s', strtotime($currentDate . $time .' '. $dimension)),
                'reason' => $reason
            ]);
        }
    }



}