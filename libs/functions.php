<?php


use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

    if (!function_exists('debug'))
    {
        function debug($data)
        {
            echo '<pre>' . print_r($data, true) . '</pre>';
            echo '<hr>';
        }
    }


    if (!function_exists('sendResponse'))
    {
        function sendResponse(int $code, array $data = null)
        {
            http_response_code($code);
            $json = array("code" => $code);
            $data ? $json = array_merge($json, $data) : null;
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    if(!function_exists('generateKey'))
    {
        function generateKey()
        {
        $generator = new ComputerPasswordGenerator();
        $generator
            ->setNumbers(true)
            ->setLength(4)
            ->setUppercase(false)
            ->setLowercase(false);
        return $generator->generatePassword();

        }
    }