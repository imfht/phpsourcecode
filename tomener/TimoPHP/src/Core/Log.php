<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Core;


use Timo\Config\Config;

class Log
{
    /**
     * 一行字符串
     */
    const LOG_TYPE_LINE = 1;

    /**
     * 多行数组
     */
    const LOG_TYPE_ARRAY = 2;

    /**
     * 写入日志
     *
     * @access public
     *
     * @param string|array $message 日志内容
     * @param string $level         日志类型  参数：Warning, Error, Notice, Debug
     * @param string $logFileName   日志文件名
     * @param int $type             日志内容类型，一行、多行数组
     *
     * @return boolean
     */
    public static function write($message, $level = 'Error', $logFileName = null, $type = self::LOG_TYPE_LINE)
    {
        if (!is_array($message)) {
            $message = ['msg' => $message];
        }

        //当日志写入功能关闭时
        if(Config::runtime('log.record') === false){
            return true;
        }

        $logFilePath = static::getLogFilePath($logFileName);
        static::makeLogFolder($logFilePath);

        //日志内容
        $message = static::buildLogContent($message, $level, $type);

        return error_log($message, 3, $logFilePath);
    }

    /**
     * 显示日志内容
     * 显示日志文件内容,以列表的形式显示.便于程序调用查看日志
     *
     * @access public
     * @param string $logFileName 所要显示的日志文件内容,默认为null, 即当天的日志文件名.注:不带后缀名.log
     * @return string
     */
    public static function show($logFileName = null)
    {
        $logFilePath    = static::getLogFilePath($logFileName);
        $logContent     = is_file($logFilePath) ? file_get_contents($logFilePath) : '';

        $logArray       = explode("\n", $logContent);
        $totalLines     = count($logArray);

        //清除不必要内存占用
        unset($logContent);

        //输出日志内容
        $html = '<table width="85%" border="0" cellpadding="0" cellspacing="1" style="background:#0478CB; font-size:12px; line-height:25px;">';

        foreach ($logArray as $key=>$logString) {

            if ($key == $totalLines - 1) {
                continue;
            }

            $bgColor = ($key % 2 == 0) ? '#FFFFFF' : '#C6E7FF';

            $html .= '<tr><td height="25" align="left" bgcolor="' . $bgColor .'">&nbsp;' . $logString . '</td></tr>';
        }

        $html .= '</table>';

        return $html;
    }

    /**
     * 创建日志内容
     *
     * @param $message
     * @param $level
     * @param int $type type=1 一行 type=2多行
     * @return string
     */
    private static function buildLogContent($message, $level, $type = self::LOG_TYPE_LINE)
    {
        $router = App::controller() . '/' . App::action();
        $remote_addr = Request::getClientIP();
        switch ($type) {
            case self::LOG_TYPE_LINE:
                $log_str = '';
                foreach ($message as $key => $val) {
                    $log_str .= '&' . $key . '=' . (!is_array($val) ? $val : var_export($val, true));
                }
                $log_str = ltrim($log_str, '&');
                $message = sprintf("[%s %s %s %s] %s\n", date('Y-m-d H:i:s'), $level, $remote_addr, $router, $log_str);
                break;
            case self::LOG_TYPE_ARRAY:
                $base = [
                    $level => sprintf('[%s %s %s]', date('Y-m-d H:i:s'), $remote_addr, $router),
                ];
                $message = var_export(array_merge($base, $message), true) . "\n";
                break;
        }
        return $message;
    }

    /**
     * 创建日志目录
     *
     * @param $logFilePath
     */
    private static function makeLogFolder($logFilePath)
    {
        $logDir = dirname($logFilePath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    /**
     * 获取当前日志文件名
     *
     * @access private
     * @param null $logFileName 日志文件名
     * @param string $prefix 文件名前缀
     * @return string
     *
     * @example
     *
     * $this->getLogFilePath('sql');
     * $this->getLogFilePath('2012-11/2012-11-23');
     */
    private static function getLogFilePath($logFileName = null, $prefix = '')
    {
        $logFileName = preg_replace("@\/|\\\@", DS, $logFileName);
        //组装日志文件路径
        $path = Config::runtime('log.path');
        if (IS_CLI) {
            $path .= 'cli' . DS;
        }
        if (!$logFileName) {
            $path .= date('Y-m') . DS . $prefix . date('d');
        } else {
            if (strpos($logFileName, DS) !== false) {
                $path .= $logFileName;
            } else {
                $path .= date('Y-m') . DS . $prefix . $logFileName;
            }
        }
        $path .= '.log';
        return $path;
    }
}
