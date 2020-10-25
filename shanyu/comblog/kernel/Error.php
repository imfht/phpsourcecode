<?php
namespace Kernel;

use Kernel\Exception\ErrorException;
use Kernel\Log;

class Error
{
    public static function register()
    {
        error_reporting(E_ALL);
        set_error_handler([__CLASS__, 'errorHandler']);
        set_exception_handler([__CLASS__, 'exceptionHandler']);
        register_shutdown_function([__CLASS__, 'shutdownFunction']);
    }

    public static function errorHandler($errno, $errstr, $errfile = '', $errline = 0, $errcontext = [])
    {
        if (error_reporting() & $errno){
            $exception = new ErrorException($errno, $errstr, $errfile, $errline, $errcontext);
            self::handler($exception);
        }
    }
/*
array (size=5)
  0 => int 8
  1 => string 'Undefined variable: nini' (length=24)
  2 => string 'E:\Service\Wamp\www\Com\application\Controller\Index.php' (length=56)
  3 => int 24
  4 => 
    array (size=0)
      empty
*/

    public static function shutdownFunction()
    {
        if (!is_null($error = error_get_last()) && self::isFatalError($error['type'])) {
            $exception = new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);
            self::handler($exception);
        }
    }
/*
  'type' => int 4
  'message' => string 'syntax error, unexpected 'return' (T_RETURN)' (length=44)
  'file' => string 'E:\Service\Wamp\www\Com\application\Controller\Index.php' (length=56)
  'line' => int 25
*/
    public static function exceptionHandler($exception)
    {
        self::handler($exception);
    }

    protected static function handler($exception)
    {
        $data = [
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'message' => $exception->getMessage(),
            'code'    => $exception->getCode(),
        ];
        $log = "[{$data['code']}] {$data['message']} [{$data['file']}:{$data['line']}]";
        echo $log;
        Log::instance()->write($log);
    }

    protected static function isFatalError($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }
}
