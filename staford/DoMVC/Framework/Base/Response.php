<?php

/**
 * 响应类
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Response
{
    public static function redirect($target = '/')
    {
        header("Location:{$url}");
        exit;
    }

    public static function notfound()
    {
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");
        exit;
    }

    public static function json(array $data)
    {
        return json_encode($data);
    }
}

?>