<?php
/**
 * Description...
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/3/28
 * Time: 14:10
 */

namespace Bjask\Message;


class SmsMessage
{
    private static $config = [];
    private static $logger = null;
    public static $instance = null;

    public static function init(Logger $logger, array $config)
    {
        if (!is_object(self::$instance) || !is_object(self::$logger)) {
            self::$config = $config;
            self::$logger = $logger;
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function send($messages = [])
    {
        $result = [];
        return $result;
    }
}