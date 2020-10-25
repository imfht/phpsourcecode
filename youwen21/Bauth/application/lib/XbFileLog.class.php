<?php

namespace Common\Controller;

/**
 * SPI 接口请求日志
 * @author baiyouwen
 */
class SpiLog
{
    public static $step = 1;
    public static $uniqid;
    public static $filename = '';

    public static function log($param, $tag = '')
    {
        self::_init();
        $str = self::_makeString($param);
        file_put_contents(APP_PATH . '/Runtime/Logs/spilog/' . self::$filename . '.txt', date('H:i:s') . "\t" . self::$uniqid . "\t" . self::$step . "\t" . $tag . "\t" . $str . PHP_EOL, FILE_APPEND);
        self::$step++;
    }

    private static function _init()
    {
        static $noCheck = 0;
        if ($noCheck) {
            return true;
        }
        if (!is_dir(APP_PATH . '/Runtime/Logs/spilog')) {
            mkdir(APP_PATH . '/Runtime/Logs/spilog');
        }
        self::$filename = date('Ymd');
        self::$uniqid = uniqid();
        $noCheck = 1;
    }

    private static function _makeString($argument)
    {
        switch (gettype($argument)) {
            case 'array':
                return self::_arrToJson($argument);
                break;
            case 'object':
            case 'resource':
                return 'object or resource';
                break;
            default:
                return $argument;
                break;
        }
    }

    private static function _arrToJson($array)
    {
        return json_encode($array);
    }

    private static function _formatStep($step)
    {
        if (strlen($step) == 1) {
            return '0' . $step;
        }
    }
}
