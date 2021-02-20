<?php


namespace App\Models;


use Exception;

class Calendar
{

    private array $calendar;

    public function __construct()
    {
            $ch = curl_init('http://xmlcalendar.ru/data/ru/2021/calendar.json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json'
            ));
            $response = curl_exec($ch);
            if(curl_errno($ch))
            {
                echo json_encode(array(
                    "error" => array(
                        "code" => 500,
                        "message" => "Ошибка запроса: " . curl_error($ch),
                        "error_code" => 1
                    )
                ), JSON_UNESCAPED_UNICODE);
                 exit();
            }
            curl_close($ch);
            $this->calendar = $this->createHolydayArray(json: $response);

    }

    /**
     * Returns array of holidays for datepicker
     */
    public function getHolidayDates(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Accept: application/json');
        http_response_code(200);
        echo json_encode($this->calendar, JSON_UNESCAPED_UNICODE);
    }

    /**Helper to form an array
     * @param string $json
     * @return array
     */
    private function createHolydayArray(string $json): array
    {
        $holydaysArray = [];
        $data = json_decode($json, true)['months'];
        foreach ($data as $key => $value) {
            $arrayOfDays = explode(',', $value['days']);
            foreach ($arrayOfDays as $item => $day) {
                strpos($day, '*') ? null :
                array_push($holydaysArray, [intval($day), intval($value['month'])]);
            }
        }
        return $holydaysArray;
    }

}