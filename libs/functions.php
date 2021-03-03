<?php
if(!function_exists('debug'))
{
    function debug($data)
    {
        echo '<pre>' . print_r($data, true) . '</pre>';
        echo '<hr>';
    }
}

//if(!function_exists('sendEmail'))
//{
//    function sendEmail($to, $subject, $message)
//    {
//        $mail = new PHPMailer\PHPMailer\PHPMailer();
//        $mail->CharSet = 'utf-8';
//        $mail->isSMTP();
//        $mail->Host = $_ENV['SMTP_HOST'];
//        $mail->Port = $_ENV['SMTP_PORT'];
//        $mail->Subject = $subject;
//        $mail->Body = $message;
//        $mail->addAddress($to, 'Dmitry');
//        try {
//            $mail->send();
//        }catch (\PHPMailer\PHPMailer\Exception $error)
//        {
//            $error->errorMessage();
//        }
//
//    }
//}

if(!function_exists('sendResponse'))
{
    function sendResponse(int $code, array $data = null)
    {
        http_response_code($code);
        $json = array("code" => $code);
        $data ? $json = array_merge($json, $data) : null;
        echo json_encode($json,JSON_UNESCAPED_UNICODE);
        exit();
    }
}