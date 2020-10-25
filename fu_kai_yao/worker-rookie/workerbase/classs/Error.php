<?php
namespace workerbase\classs;

/**
 * 异常处理类
 * Class Error
 * @package workerbase\classs
 * @author fukaiyao
 */
class Error
{
    /**
     * 注册异常处理
     * @access public
     * @return void
     */
    public static function register()
    {
        error_reporting(E_ALL);
        set_error_handler([__CLASS__, 'appError']);
        set_exception_handler([__CLASS__, 'appException']);
        register_shutdown_function([__CLASS__, 'appShutdown']);
    }

    /**
     * 异常处理
     * @access public
     * @param  \Exception|\Throwable $e 异常
     * @throws \Exception
     */
    public static function appException($e)
    {
        if (!$e instanceof \Exception) {
            if ($e instanceof \ParseError) {
                $message  = 'Parse error: ' . $e->getMessage();
                $severity = E_PARSE;
            } elseif ($e instanceof \TypeError) {
                $message  = 'Type error: ' . $e->getMessage();
                $severity = E_RECOVERABLE_ERROR;
            } else {
                $message  = 'Fatal error: ' . $e->getMessage();
                $severity = E_ERROR;
            }

            $e = new \ErrorException($message, $e->getCode(), $severity,  $e->getFile(), $e->getLine());
        }

        self::exceptionHandler($e);

    }

    /**
     * 错误处理
     * @access public
     * @param  integer $errno      错误编号
     * @param  integer $errstr     详细错误信息
     * @param  string  $errfile    出错的文件
     * @param  integer $errline    出错行号
     * @throws \ErrorException
     * @throws \Exception
     */
    public static function appError($errno, $errstr, $errfile = '', $errline = 0, $errcontext = '')
    {
//        $exception = new \ErrorException($errstr, 0, $errno, $errfile, $errline);
//
//        // 符合异常处理的则将错误信息托管至\ErrorException
//        if (error_reporting() & $errno) {
//            throw $exception;
//        }

        //这里调用异常处理函数记录日志后，要重新抛出异常，否则程序进程将退出
        if (self::exceptionHandler(new \ErrorException($errstr, 0, $errno,  $errfile, $errline))) {
            throw new \ErrorException($errstr, 0, $errno,  $errfile, $errline);
        }
    }

    /**
     * 异常中止处理
     * @access public
     * @return void
     */
    public static function appShutdown()
    {
        App::end(false);
        // 将错误信息托管至 \ErrorException
        if (!is_null($error = error_get_last()) && self::isFatal($error['type'])) {
            self::appException(new \ErrorException(
                 $error['message'], 0, $error['type'], $error['file'], $error['line']
            ));
        }
    }

    /**
     * 确定错误类型是否致命
     * @access protected
     * @param  int $type 错误类型
     * @return bool
     */
    protected static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    /**
     * 获取异常处理的实例
     * @access public
     * @param  object $e     异常对象
     * @throws \Exception
     */
    public static function exceptionHandler($e)
    {
        $env = Config::getInstance()->get('env');
        // 收集异常数据
        if (in_array($env, ['dev', 'test'])) {
            $data = [
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'message' => self::getMessage($e),
                'code'    => self::getCode($e),
                'source'  => self::getSourceCode($e),
            ];
            $log = "[{$data['code']}]{$data['message']}[{$data['file']}:{$data['line']}]";
            $log .= "\r\n" . $e->getTraceAsString();
        } else {
            $data = [
                'code'    => self::getCode($e),
                'message' => self::getMessage($e),
            ];
            $log = "[{$data['code']}]{$data['message']}";
        }

        Log::error($log);

        if (in_array($env, ['dev', 'test', 'local_debug'])) {
//            throw new \RuntimeException($log);
            //打印错误信息
            echo "\n";
            var_dump($log);
            var_export($log);
            echo "\n";
        }
        return true;
    }

    /**
     * 获取错误编码
     * ErrorException则使用错误级别作为错误编码
     * @param  \Exception $exception
     * @return integer                错误编码
     */
    public static function getCode(\Exception $exception)
    {
        $code = $exception->getCode();
        if (!$code && $exception instanceof \ErrorException) {
            $code = $exception->getSeverity();
        }
        return $code;
    }

    /**
     * 获取错误信息
     * ErrorException则使用错误级别作为错误编码
     * @param  \Exception $exception
     * @return string                错误信息
     */
    public static function getMessage(\Exception $exception)
    {
        $message = $exception->getMessage();
        return $message;
    }

    /**
     * 获取出错文件内容
     * 获取错误的前9行和后9行
     * @param  \Exception $exception
     * @return array                 错误文件内容
     */
    public static function getSourceCode(\Exception $exception)
    {
        // 读取前9行和后9行
        $line  = $exception->getLine();
        $first = ($line - 9 > 0) ? $line - 9 : 1;

        try {
            $contents = file($exception->getFile());
            $source   = [
                'first'  => $first,
                'source' => array_slice($contents, $first - 1, 19),
            ];
        } catch (\Exception $e) {
            $source = [];
        }
        return $source;
    }
}
