<?php namespace qeephp\debug;

require_once PACKAGES_PATH . '/3rd/FirePHP.class.php';

use qeephp\tools\ILogger;
use \FirePHP;

class AlertFb
{

    static function alert($level, $message)
    {
        if ( headers_sent() )
        {
            return;
        }
        switch ($level)
        {
            case ILogger::TRACE:
                FirePHP::getInstance(true)->trace($message);
                break;
            case ILogger::DEBUG:
                FirePHP::getInstance(true)->info($message);               
                break;
            case ILogger::INFO:
                FirePHP::getInstance(true)->log($message);
                break;
            case ILogger::WARN:
                FirePHP::getInstance(true)->warn($message);
                break;
            case ILogger::ERROR:
                FirePHP::getInstance(true)->error($message,FirePHP::EXCEPTION);
                break;
            case ILogger::FATAL:
                FirePHP::getInstance(true)->error($message,FirePHP::ERROR);
                break;
        }
    }

}