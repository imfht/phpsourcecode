<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 日志类
 */

namespace onefox;

final class Log {

    //日志级别常量
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';

    const LOG_DEFAULT_KEYWORD = 'log_msg';

    private static $_instance = null;
    //默认配置
    private $_config = [
        'ext' => '.log',//日志文件类型
        'date_format' => 'Y-m-d H:i:s',//日期格式
        'filename' => '',//日志文件名
        'log_path' => '',//日志路径
        'prefix' => 'onefox_',//日志文件名前缀
        'log_level' => 'info',//日志输出级别
        'log_seperator' => '|',//日志输出内容分隔符
        'log_kv_seperator' => '=',//日志内容中的键值分隔符
        'log_error_ext' => '.wf',//错误日志输出后缀
        'log_debug_ext' => '.dt',//调试日志输出后缀
    ];
    //日志文件
    private $_logFile = '';
    //日志级别
    private $_logLevels = [
        'emergency' => 0,
        'alert' => 1,
        'critical' => 2,
        'error' => 3,
        'warning' => 4,
        'notice' => 5,
        'info' => 6,
        'debug' => 7
    ];

    /**
     * 实例化类
     * @param string $confStr 配置
     * @return object
     */
    public static function instance($confStr = 'default') {
        if (!self::$_instance) {
            //注意new self的使用
            self::$_instance = new self($confStr);
        }
        return self::$_instance;
    }

    //单例模式
    private function __construct($confStr) {
        $this->setConfig($confStr);
        $this->_setLogFile();
        if (file_exists($this->_logFile) && !is_writable($this->_logFile)) {
            throw new \RuntimeException('没有文件写入权限');
        }
    }

    /**
     * @param string $confStr 配置
     */
    public function setConfig($confStr = 'default') {
        $config = Config::get('log.' . $confStr);
        $config && $this->_config = array_merge($this->_config, $config);
    }

    /**
     * 写入日志
     * 日志级别(由低到高): debug->info->notice->warning->error->critical->alert->emergency
     * @param string|array $msg
     * @param string $level
     * @return boolean
     */
    public function save($msg, $level = 'info') {
        if (!$msg) {
            return false;
        }
        if ($this->_logLevels[$this->_config['log_level']] < $this->_logLevels[$level]) {
            return false;
        }
        //处理日志输出文件
        if ($this->_logLevels[$level] < $this->_logLevels[self::NOTICE]) {
            $this->_logFile .= $this->_config['log_error_ext'];
        } elseif ($this->_logLevels[$level] == $this->_logLevels[self::DEBUG]) {
            $this->_logFile .= $this->_config['log_debug_ext'];
        }
        if (!is_array($msg)) {
            $msg = array(self::LOG_DEFAULT_KEYWORD => $msg);
        }
        $content = $this->_getDate() . $this->_config['log_seperator'] . strtoupper($level);
        foreach ($msg as $key => $val) {
            if (is_array($val)) {
                $val = json_encode($val);//数组转化成json输出
            }
            $content .= $this->_config['log_seperator'] . $key . $this->_config['log_kv_seperator'] . $val;
        }
        $content .= PHP_EOL;
        return file_put_contents($this->_logFile, $content, FILE_APPEND);
    }

    /**
     * @param $msg
     * @param string|array $config
     * @return mixed
     */
    public static function debug($msg, $config = 'default') {
        return self::instance($config)->save($msg, self::DEBUG);
    }

    /**
     * @param $msg
     * @param string|array $config
     * @return mixed
     */
    public static function info($msg, $config = 'default') {
        return self::instance($config)->save($msg, self::INFO);
    }

    /**
     * @param $msg
     * @param string|array $config
     * @return mixed
     */
    public static function notice($msg, $config = 'default') {
        return self::instance($config)->save($msg, self::NOTICE);
    }

    /**
     * @param $msg
     * @param string|array $config
     * @return mixed
     */
    public static function warning($msg, $config = 'default') {
        return self::instance($config)->save($msg, self::WARNING);
    }

    /**
     * @param $msg
     * @param string|array $config
     * @return mixed
     */
    public static function error($msg, $config = 'default') {
        return self::instance($config)->save($msg, self::ERROR);
    }

    /**
     * 获取时间格式
     */
    private function _getDate() {
        $originalTime = microtime(true);
        $micro = sprintf("%06d", ($originalTime - floor($originalTime)) * 1000000);
        $date = new \DateTime(date('Y-m-d H:i:s.' . $micro, $originalTime));
        return $date->format($this->_config['date_format']);
    }

    /**
     * 设置存入文件
     */
    private function _setLogFile() {
        $log_dir = $this->_config['log_path'];
        if (!$log_dir) {
            $log_dir = LOG_PATH;
        }
        $log_dir = rtrim($log_dir, DS);
        C::mkDirs($log_dir);//创建目录
        if ($this->_config['filename']) {
            if (strpos($this->_config['filename'], '.log') !== false || strpos($this->_config['filename'], '.txt') !== false) {
                $this->_logFile = $log_dir . DS . $this->_config['filename'];
            } else {
                $this->_logFile = $log_dir . DS . $this->_config['filename'] . $this->_config['ext'];
            }
        } else {
            $this->_logFile = $log_dir . DS . $this->_config['prefix'] . date('YmdH') . $this->_config['ext'];
        }
    }

}
