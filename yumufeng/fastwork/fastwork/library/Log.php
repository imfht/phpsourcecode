<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 19:05
 */

namespace fastwork;

use fastwork\facades\Env;

class Log
{
    /**
     * 配置参数
     * @var array
     */
    private $config = [];

    private static $logs = [];

    public function __construct(Config $config)
    {

        $this->config = $config->pull('log');
    }

    public static function __make(Config $config)
    {
        $request = new static($config);
        return $request;
    }

    /**
     * 写入日志
     * @param       $type
     * @param array $params
     * @return bool
     */
    public function record($type, $params)
    {
        $type = strtoupper($type);
        if (is_array($params)) {
            $params = print_r($params, true);
        } elseif (is_object($params)) {
            $params = json_decode($params);
        }
        $msg = "{$type} \t " . date("Y-m-d h:i:s") . " \t " . $params;
        if (!in_array($type, $this->config['level'])) return false;
        self::$logs[$type][] = $msg;
    }

    /**
     * swoole异步写入日志信息
     * @return bool
     */
    public function save()
    {
        if (!empty(self::$logs)) {
            $path = Env::get('runtime_path');
            $dir_path = $path . 'log/' . date('Ym') . DIRECTORY_SEPARATOR;
            !is_dir($dir_path) && @mkdir($dir_path, 0777, TRUE);
            foreach (self::$logs as $type => $logs) {
                $filename = date('d') . '_' . $type . '.log';
                $filepath = $dir_path . $filename;
                $content = NULL;
                foreach ($logs as $log) {
                    $content .= $log . PHP_EOL;
                }
                swoole_async_writefile($filepath, $content, NULL, FILE_APPEND);
            }
            self::$logs = [];
        }
        return true;
    }

    /**
     * 定时保存日志
     * @param \swoole_server $server
     */
    public function clearTimer(\swoole_server $server)
    {
        $log_save_time = isset($this->config['save_time']) ? $this->config['save_time'] : 3000;
        $server->tick($log_save_time, function () {
            $this->save();
        });
    }

    /**
     * 记录日志信息
     * @access public
     * @param  string $level 日志级别
     * @param  mixed $message 日志信息
     * @param  array $context 替换内容
     * @return void
     */
    public function log($level, $message)
    {
        $this->record($level, $message);
    }

    /**
     * 记录emergency信息
     * @access public
     * @param  mixed $message 日志信息
     * @param  array $context 替换内容
     * @return void
     */
    public function emergency($message)
    {
        $this->log(__FUNCTION__, $message);
    }

    /**
     * 记录警报信息
     * @access public
     * @param  mixed $message 日志信息
     * @param  array $context 替换内容
     * @return void
     */
    public function alert($message)
    {
        $this->log(__FUNCTION__, $message);
    }

    /**
     * 记录紧急情况
     * @access public
     * @param  mixed $message 日志信息
     * @param  array $context 替换内容
     * @return void
     */
    public function critical($message)
    {
        $this->log(__FUNCTION__, $message);
    }

    /**
     * 记录错误信息
     * @access public
     * @param  mixed $message 日志信息
     * @param  array $context 替换内容
     * @return void
     */
    public function error($message)
    {
        $this->log(__FUNCTION__, $message);
    }

    /**
     * 记录warning信息
     * @access public
     * @param  mixed $message 日志信息
     * @param  array $context 替换内容
     * @return void
     */
    public function warning($message)
    {
        $this->log(__FUNCTION__, $message);
    }

    /**
     * 记录notice信息
     * @access public
     * @param  mixed $message 日志信息
     * @param  array $context 替换内容
     * @return void
     */
    public function notice($message)
    {
        $this->log(__FUNCTION__, $message);
    }

    /**
     * 记录一般信息
     * @access public
     * @param  mixed $message 日志信息
     * @param  array $context 替换内容
     * @return void
     */
    public function info($message)
    {
        $this->log(__FUNCTION__, $message);
    }

    /**
     * 记录调试信息
     * @access public
     * @param  mixed $message 日志信息
     * @param  array $context 替换内容
     * @return void
     */
    public function debug($message)
    {
        $this->log(__FUNCTION__, $message);
    }

    /**
     * 记录sql信息
     * @access public
     * @param  mixed $message 日志信息
     * @param  array $context 替换内容
     * @return void
     */
    public function sql($message)
    {
        $this->log(__FUNCTION__, $message);
    }
}