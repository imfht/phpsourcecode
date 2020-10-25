<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Exception;


use Exception;
use Timo\Core\App;
use Timo\Core\Response;
use Timo\Log\Log;

class CoreException extends Exception
{
    public function __construct($message, $code = 0, $file = '', $line = '')
    {
        parent::__construct($message, $code);
        $this->message = $message;
        $this->code = $code;
        if (!empty($file)) {
            $this->file = $file;
        }
        if (!empty($line)) {
            $this->line = $line;
        }
    }

    /**
     * 异常处理
     *
     * @param Exception $e
     * @param int $code
     * @param string $msg
     */
    public static function handle($e, $code = 500, $msg = 'Internet Server Error')
    {
        //记录日志
        $no_log = [40001, 40004];
        if (!in_array($e->getCode(), $no_log)) {
            $log = self::buildLog($e);
            Log::single(print_r($log, true), 'Exception/' . date('m.d'));
        } else {
            $code = 404;
            $msg = 'App('. APP_NAME . ') ' . $e->getMessage();
        }
        Response::sendResponseCode($code);
        Response::send(App::result($code, $msg), 'json');
    }

    /**
     * 生成404错误日志
     *
     * @param Exception $e
     * @return array
     */
    public static function buildLog($e)
    {
        $log = [];
        $log['Message'] = $e->getMessage();
        $log['Code'] = $e->getCode();
        $log['File'] = $e->getFile();
        $log['Line'] = $e->getLine();
        $log['trace'] = explode("\n", $e->getTraceAsString());

        return $log;
    }
}
