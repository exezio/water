<?php
if(!function_exists('debug'))
{
    function debug($data)
    {
        echo '<pre>' . print_r($data, true) . '</pre>';
        echo '<hr>';
    }
}

if(!function_exists('sendEmail'))
{
    function sendEmail($to, $subject, $message)
    {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->CharSet = 'utf-8';
        $mail->isSMTP();
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->addAddress($to, 'Dmitry');
        try {
            $mail->send();
        }catch (\PHPMailer\PHPMailer\Exception $error)
        {
            $error->errorMessage();
        }

    }
}