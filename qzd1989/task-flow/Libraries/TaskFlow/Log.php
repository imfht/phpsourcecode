<?php
/**
 * 日志
 */
namespace TaskFlow\Libraries\TaskFlow;

use TaskFlow\Libraries\TaskFlow\Log\Hander;
use Monolog\Logger;

class Log
{
    public static function __callStatic($levelName, $arguments)
    {
        $levels    = Logger::getLevels();
        $levelName = strtoupper($levelName);
        $level     = $levels[$levelName];

        $logConfig = require TASKFLOW_ROOT . 'Config/Log.php';
        $logConfig = $logConfig['connections'][$logConfig['default']];

        if ($logConfig['channel'] == 'stack') {
            $url = $logConfig['dir'] . 'task.log';
        }

        if ($logConfig['channel'] == 'daily') {
            $url = $logConfig['dir'] . date('Y-m-d', time()) . '.log';
        }

        $handler = new Hander($url, $level);
        $data    = ['level' => $level, 'levelStr' => $levelName, 'levels' => $levels];

        if (is_array($arguments[0])) {
            $data['message'] = null;
            $data['context'] = $arguments[0];
        } else {

            $data['message'] = $arguments[0];
        }

        if (isset($arguments[1])) {
            $data['context'] = $arguments[1];
        }

        $handler->write($data);
    }
}
