<?php
namespace Framework;

class SZLogger {
    const INFO_LEVEL = 'INFO';

    const DEBUG_LEVEL = 'DEBUG';

    const WARN_LEVEL = 'WARN';

    const ERROR_LEVEL = 'ERROR';

    private static $levels = array(
        self::DEBUG_LEVEL => 0,
        self::INFO_LEVEL => 1,
        self::WARN_LEVEL => 2,
        self::ERROR_LEVEL => 3,
    );

    private static function parseParmas($params) {
        if (count($params) == 0) {
            $message = $params[0];
        } else {
            $messages = array();
            foreach ($params as $param) {
                if (is_scalar($param)) {
                    $messages[] = $param;
                } else {
                    $messages[] = json_encode($param);
                }
            }
            $message = join(', ', $messages);
        }

        return $message;
    }

    public static function debug() {
        $message = self::parseParmas(func_get_args());

        self::log($message, self::DEBUG_LEVEL);
    }

    public static function info() {
        $message = self::parseParmas(func_get_args());

        self::log($message, self::INFO_LEVEL);
    }

    public static function warn() {
        $message = self::parseParmas(func_get_args());

        self::log($message, self::WARN_LEVEL);
    }

    public static function error() {
        $message = self::parseParmas(func_get_args());

        self::log($message, self::ERROR_LEVEL);
    }


    private static function log($message, $label = 'LOG') {
        $timelabel = date("Y:m:d H:i:s");
        $buffer = sprintf("[%s]: %s - %s (%s)\n", $label, $message, $timelabel, posix_getpid());

        $log_modes = SZConfig::Instance()->get('log_mode');

        foreach ($log_modes as $log_mode) {
            self::$log_mode($buffer);
        }
    }

    private static function console($buffer) {
        echo $buffer;
    }

    private static function file($buffer) {
        $logpath = SZConfig::Instance()->get('log_path');

        file_put_contents($logpath, $buffer, FILE_APPEND);
    }
}