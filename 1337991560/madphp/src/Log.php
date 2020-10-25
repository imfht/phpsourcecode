<?php

/**
 * Log
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;

class Log
{
    protected static $logPath;
    protected static $threshold = null;
    protected static $dateFmt = 'Y-m-d H:i:s';
    protected static $enabled = TRUE;
    protected static $levels = array('ERROR' => '1', 'DEBUG' => '2',  'INFO' => '3', 'ALL' => '4');

    private static function init()
    {
        $config = Config::get('log');

        if (!defined('LOG_PATH')) {
            throw new \InvalidArgumentException("LOG_PATH is undefined!");
        }

        self::$logPath = (isset($config['logPath']) && $config['logPath'] != '') ? $config['logPath'] : LOG_PATH;

        if (!is_dir(self::$logPath) OR !is_really_writable(self::$logPath)) {
            mkdirs(self::$logPath);
            if (!is_dir(self::$logPath) OR !is_really_writable(self::$logPath)) {
                self::setEnabled(FALSE);
            }
        }

        self::initThreshold();

        if (isset($config['logDateFormat']) && $config['logDateFormat'] != '') {
            self::$dateFmt = $config['logDateFormat'];
        }
        
        if (!defined('FILE_WRITE_MODE')) {
            throw new \InvalidArgumentException("FILE_WRITE_MODE is undefined!");
        }

        if (!defined('FOPEN_WRITE_CREATE')) {
            throw new \InvalidArgumentException("FOPEN_WRITE_CREATE is undefined!");
        }
    }

    /**
     * 写日志
     * @param   string  the log level
     * @param   string  the log message
     * @param   bool    是否忽略配置信息强制写日志
     * @return  bool
     */
    public static function write($level = 'error', $msg, $force = false)
    {
        self::init();

        if (self::isEnabled() === FALSE) {
            return FALSE;
        }

        $level = strtoupper($level);

        if (!isset(self::$levels[$level])) {
            return FALSE;
        }

        if (!$force && ((self::$levels[$level] > self::getThreshold()) || (self::getThreshold() === null))) {
            return false;
        }

        $filepath = self::$logPath.'log-'.date('Y-m-d').'.php';
        $message  = '';

        if (!file_exists($filepath)) {
            $message .= "<"."?php ?".">".PHP_EOL.PHP_EOL;
        }

        if (!$fp = @fopen($filepath, FOPEN_WRITE_CREATE)) {
            return FALSE;
        }

        $message .= $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date(self::$dateFmt). ' --> '.$msg.PHP_EOL;

        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);

        @chmod($filepath, FILE_WRITE_MODE);
        return TRUE;
    }

    /**
     * 调试日志
     * @param  string 日志内容
     * @param  bool   是否忽略配置信息强制写日志
     * @return bool What the Logger returns, or false if Logger not set or not enabled
     */
    public static function debug($msg, $force = false)
    {
        return self::write('debug', $msg, $force);
    }

    /**
     * 错误日志
     * @param  string 日志内容
     * @param  bool   是否忽略配置信息强制写日志
     * @return bool What the Logger returns, or false if Logger not set or not enabled
     */
    public static function error($msg, $force = false)
    {
        return self::write('error', $msg, $force);
    }

    /**
     * 信息日志
     * @param  string 日志内容
     * @param  bool   是否忽略配置信息强制写日志
     * @return bool What the Logger returns, or false if Logger not set or not enabled
     */
    public static function info($msg, $force = false)
    {
        return self::write('info', $msg, $force);
    }

    /**
     * 所有日志
     * @param  string 日志内容
     * @param  bool   是否忽略配置信息强制写日志
     * @return bool What the Logger returns, or false if Logger not set or not enabled
     */
    public static function all($msg, $force = false)
    {
        return self::write('all', $msg, $force);
    }

    /**
     * 设置是否启用Log
     * @param  bool $enabled
     */
    private static function setEnabled($enabled)
    {
        if ($enabled) {
            self::$enabled = true;
        } else {
            self::$enabled = false;
        }
    }

    /**
     * 是否启用Log
     * @return bool
     */
    private static function isEnabled()
    {
        return self::$enabled;
    }

    /**
     * 设置写日志阈值
     * @param  int                          $threshold
     * @throws \InvalidArgumentException    If invalid log threshold specified
     */
    private static function setThreshold($threshold)
    {
        $thresholds = self::$levels;
        $thresholds['NULL'] = 0;
        if (!in_array($threshold, $thresholds)) {
            throw new \InvalidArgumentException('Invalid log threshold');
        }
        self::$threshold = $threshold;
    }

    /**
     * 获取写日志阈值
     * @return int
     */
    private static function getThreshold()
    {
        return self::$threshold;
    }

    /**
     * 初始化 Threshold 为配置值
     * @return void
     */
    private static function initThreshold()
    {
        $logThreshold = Config::get('log', 'logThreshold', null);
        if (is_numeric($logThreshold)) {
            self::setThreshold($logThreshold);
        } else {
            self::$threshold = null;
        }
    }
}
// END Log Class

/* End of file Log.php */