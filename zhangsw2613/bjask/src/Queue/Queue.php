<?php
/**
 * Description...
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/3/28
 * Time: 13:12
 */

namespace Bjask\Queue;

use Bjask\Logger;

class Queue
{
    private static $config = [];
    private static $queue = null;

    public static function getQueue(array $config, Logger $logger)
    {
        empty(self::$config) && self::$config = $config;
        if (!is_object(self::$queue)) {
            try {
                $class_name = 'Bjask\\Queue\\' . ucfirst(self::$config['driver'] . 'Queue');
                if (!class_exists($class_name)) {
                    throw new \Exception("找不到队列类：{$class_name}");
                }
                $class = new $class_name(self::$config);
                if (!$class instanceof QueueHandlerInterface) {
                    throw new \Exception("队列类{$class}未继承自QueueHandlerInterface接口类");
                }
                self::$queue = $class;
            } catch (\Exception $e) {
                $logger->log($e->getMessage(), $logger::LEVEL_ERROR);
            } catch (\Throwable $e) {
                $logger->log($e->getMessage(), $logger::LEVEL_ERROR);
            }
        }
        return self::$queue;
    }
}