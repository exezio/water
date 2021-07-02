<?php


namespace App\Controllers;


use App\Models\Calendar;
use App\Models\RefreshToken;
use App\Models\CheckAuth;
use App\Models\Security;

class SubrequestController
{
    public function calendarAction()
    {
        $calendar = new Calendar();
        $calendar->getHolidayDates();
    }

    public function getDepartmentsListAction()
    {
        echo 'kek';
    }

    public function refreshTokenAction()
    {
            $refreshToken = new RefreshToken();
            $result = $refreshToken->refreshToken();
            $result ? sendResponse(code: 200, data: $result) : $refreshToken::getError();
    }


}