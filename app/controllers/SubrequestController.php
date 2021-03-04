<?php


namespace App\Controllers;


use App\Models\Calendar;

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

}