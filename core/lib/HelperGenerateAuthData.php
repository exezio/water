<?php


namespace Core\lib;

use Browser;

trait HelperGenerateAuthData
{

    public function generateSessionData(): array
    {
        $browserLib = new Browser();
        $platform = $browserLib->getPlatform();
        $browser = $browserLib->getBrowser();
        $browserVersion = $browserLib->getVersion();
        $userAgent = $browserLib->getUserAgent();
        $sessionData = [
            'access_token' => bin2hex(random_bytes(16)),
            'expires' => strtotime("+{$_ENV['TOKEN_LIFETIME']} minutes"),
            'refresh_token' => bin2hex(random_bytes(16)),
            'secret' => hash("md5", generateKey()),
            'browser' => $browser,
            'browser_version' => $browserVersion,
            'user_agent' => $userAgent
        ];
        $userSessionData = [
            'Access_token' => $sessionData['access_token'],
            'Expires' => $sessionData['expires'],
            'Refresh_token' => $sessionData['refresh_token']
        ];
        return compact('sessionData', 'userSessionData', 'platform');
    }


}