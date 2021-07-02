<?php


namespace App\Models;


use Core\lib\DataBase;
use Core\Model;
use DateTime;

class Calendar extends Model
{


    public static function checkDeliveryDate(
        string $dateDelivery,
        string|int $departmentCode,
        string|int $deliveryPlaceCode
    ): bool {
        $client = DataBase::instance()->getClient();
        $calendarCollection = $client->water->calendar;
        $holidays = (array)$calendarCollection->findOne([
            'year' => (int)date('Y'),
            'for' => 'api'
        ]) ?: self::getCalendarForApi();
        $dateDelivery = date('d.m.Y', strtotime($dateDelivery));
        $minNextDayOfDelivery = self::getMinDayDelivery(holidays: $holidays, departmentCode: $departmentCode, deliveryPlaceCode: $deliveryPlaceCode);
        if (
            in_array($dateDelivery, (array)$holidays['days']) ||
            strtotime($dateDelivery) < strtotime($minNextDayOfDelivery) ||
            $departmentCode == $_ENV['SPECIFIC_DEPARTMENT'] &&
            $deliveryPlaceCode == $_ENV['SPECIFIC_PLACE'] &&
            date('D', strtotime($dateDelivery)) == $_ENV['SPECIFIC_DAY'] ||
            date('m.Y', strtotime($dateDelivery)) != date('m.Y') &&
            date('m.Y', strtotime($dateDelivery)) != date('m.Y', strtotime('+1 month'))
        ) {
            self::addError(400,
                "Доставка {$dateDelivery} недоступна, ближайший возможный день доставки {$minNextDayOfDelivery}");
            return false;
        }
        return true;
    }


    private static function fillCalendar(): array
    {
        $client = DataBase::instance()->getClient();
        $calendarCollection = $client->water->calendar;
        $ch = curl_init("http://xmlcalendar.ru/data/ru/" . date('Y') . "/calendar.json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/json'
        ));
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            self::addError(400, 'Не удалось загрузить календарь');
        }
        curl_close($ch);
        $holidays = self::createCalendars(json: $response);
        $calendarCollection->insertMany([$holidays['calendarForAPI'], $holidays['calendarForDatepicker']]);
        return $holidays;
    }

    /**Helper to form an array
     * @param string $json
     * @param object $calendarCollection
     * @return array
     */
    private static function createCalendars(string $json): array
    {
        $data = json_decode($json, true);
        $calendarForAPI = ['for' => 'api', 'year' => $data['year'], 'days' => []];
        $calendarForDatepicker = ['for' => 'datepicker', 'year' => $data['year'], 'days' => []];
        foreach ($data['months'] as $key => $value) {
            $arrayOfDays = explode(',', $value['days']);
            foreach ($arrayOfDays as $item => $day) {
                if (!strpos($day, '*')) {
                    array_push($calendarForDatepicker['days'], [intval($day), intval($value['month'])]);
                    array_push($calendarForAPI['days'],
                        date('d.m.Y', strtotime("{$day}.{$value['month']}.{$data['year']}")));
                }
            }
        }
        return compact('calendarForAPI', 'calendarForDatepicker');
    }

    private static function getCalendarForApi(): array
    {
        $calendars = self::fillCalendar();
        return $calendars['calendarForAPI'];
    }

    public function getCalendarForDatepicker(): array
    {
        $client = DataBase::instance()->getClient();
        $calendarCollection = $client->water->calendars;
        $calendar = (array)$calendarCollection->findOne([
            'year' => (int)date('Y'),
            'for' => 'datepicker'
        ]) ?: self::fillCalendar();
        return $calendar['calendarForDatepicker'];
    }

    private static function getMinDayDelivery(array $holidays, int $departmentCode, int $deliveryPlaceCode)
    {
        $currentHour = (int)date('H');
        $minNextDayOfDelivery = (($currentHour <= $_ENV['LAST_HOUR_OF_ORDER']) ?
            date('d.m.Y', strtotime('+1 day')) :
            date('d.m.Y', strtotime('+2 day')));
        while (
            in_array($minNextDayOfDelivery, (array)$holidays['days']) ||
            $departmentCode == $_ENV['SPECIFIC_DEPARTMENT'] &&
            $deliveryPlaceCode == $_ENV['SPECIFIC_PLACE'] &&
            date('D', strtotime($minNextDayOfDelivery)) == $_ENV['SPECIFIC_DAY']
        ) {
            $minNextDayOfDelivery = date('d.m.Y', strtotime($minNextDayOfDelivery . '+1 day'));
        }
        return $minNextDayOfDelivery;
    }

    public static function getMonthsList()
    {
        return [
          '1' => 'Январь',
          '2' => 'Февраль',
          '3' => 'Март',
          '4' => 'Апрель',
          '5' => 'Май',
          '6' => 'Июнь',
          '7' => 'Июль',
          '8' => 'Август',
          '9' => 'Сентябрь',
          '10' => 'Октябрь',
          '11' => 'Ноябрь',
          '12' => 'Декабрь'
        ];
    }


}