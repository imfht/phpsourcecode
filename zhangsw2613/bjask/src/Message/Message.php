<?php
/**
 * Description...
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/3/28
 * Time: 14:10
 */

namespace Bjask\Message;


use Bjask\Logger;

class Message
{
    public static $message = null;

    public static function init(Logger $logger, string $message_type, array $config)
    {
        if (is_null(self::$message)) {
            try {
                $class = 'Bjask\\Message\\' . ucfirst($message_type . 'Message');
                if (!is_callable([$class, 'init'])) {
                    throw new \Exception("message class call undefined method:init");
                }
                self::$message = $class::init($logger, $config[$message_type]);
            } catch (\Exception $e) {
                $logger->log($e->getMessage(), $logger::LEVEL_ERROR);
            } catch (\Throwable $e) {
                $logger->log($e->getMessage(), $logger::LEVEL_ERROR);
            }
        }
        return self::$message;
    }
}