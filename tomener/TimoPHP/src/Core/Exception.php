<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Core;


class Exception extends \Exception
{
    /**
     * 异常处理
     *
     * @param \Exception $e
     * @param int $code
     * @param string $msg
     */
    public static function handle($e, $code = 404, $msg = '资源不存在')
    {
        //记录日志
        $no_log = [40001, 40004];
        if (!in_array($e->getCode(), $no_log)) {
            $log = self::buildLog($e);
            Log::write(print_r($log, true), 'Error', 'Exception/' . date('m.d'));
        } else {
            $msg = 'App '. APP_NAME .' Controller or Action not exists.';
        }
        Response::send(App::result($code, $msg), 'json');
    }

    /**
     * 获取异常信息
     *
     * @param \Exception $e
     * @param bool $output
     * @return bool|string
     */
    public static function buildHtml($e, $output = false)
    {
        $html = '<div class="exception">';
        $html .= '<p>[' . $e->getCode() . '] Exception in ' . $e->getFile() . ' Line '
            . $e->getLine() . '</p>';
        $html .= '<p>Notice: ' . $e->getMessage() . '</p>';
        if (!$output) {
            return $html;
        } else {
            echo $html;
            return true;
        }
    }

    /**
     * 生成404错误日志
     *
     * @param \Exception $e
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
