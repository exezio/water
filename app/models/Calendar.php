<?php


namespace App\Models;


use Exception;

class Calendar
{

    public function getHolidayDates(): void
    {
        try {
            $ch = curl_init('http://xmlcalendar.ru/data/ru/2021/calendar.json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json'
            ));
            $response = curl_exec($ch);
            curl_close($ch);
            $response = $this->createHolydayArray(json: $response);
            header('Access-Control-Allow-Origin: *');
            header('Accept: application/json');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        } catch (Exception $error) {
            $error->getMessage();
            http_response_code(404);
        }
    }

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