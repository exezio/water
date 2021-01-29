<?php
if(!function_exists('debug'))
{
    function debug($data)
    {
        echo '<pre>' . print_r($data, true) . '</pre>';
        echo '<hr>';
    }
}