<?php
/**
 * 日志处理类
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/3/26
 * Time: 11:27
 */

namespace Bjask;

class Logger
{
    const LEVEL_ERROR = "error";
    const LEVEL_WARNING = "warning";
    const LEVEL_NOTICE = "notice";
    const LEVEL_INFO = "info";

    const prefix = "bjask";//默认日志文件前缀
    const logPath = "logs";//默认logs目录
    const maxFileSize = 100; // 默认单日志文件最大100MB
    const maxBuffer = 10000;//默认最大缓冲量

    private static $instance = null;
    private static $message = [];
    private static $config = [];

    private function __construct($config)
    {
        self::$config = array_replace_recursive([
            'prefix' => self::prefix,
            'log_path' => self::logPath,
            'max_file_size' => self::maxFileSize,
            'max_buffer' => self::maxBuffer
        ], $config);
        if (!is_dir(self::$config['log_path'])) {
            mkdir(self::$config['log_path'], 0755, true);
        }
    }

    /**
     * 实例化单利
     * @param array $config
     * @return Logger
     */
    public static function getInstance(array $config): Logger
    {
        if (self::$instance === null) {
            self::$instance = new self(array_filter($config));
        }
        return self::$instance;
    }

    /**
     * 日志写入处理
     * @param $message
     * @param string $level
     * @param bool $immediately
     * @return bool
     */
    public function log($message, $level = "info", $immediately = true)
    {
        if (empty($message)) {
            return false;
        }
        self::$message[] = self::formatMessage($message, $level, time());
        if ($immediately || (!$immediately && count(self::$message) >= self::$config['max_buffer'])) {
            self::write($message);
        }
    }

    /**
     * 格式化日志信息
     * @param $message
     * @param $level
     * @param $time
     * @return string
     */
    private static function formatMessage($message, $level, $time): string
    {
        return "[{$level}][" . date('Y-m-d H:i:s', $time) . "]" . $message . PHP_EOL;
    }

    /**
     * 写入日志文件
     */
    private static function write()
    {
        $file = self::$config['log_path'] . DIRECTORY_SEPARATOR . self::$config['prefix'] . '_' . date('Y-m-d') . '.log';
        if (($fp = @fopen($file, 'a')) === false) {
            throw new \Exception("日志文件：{$file}打开失败！");
        }
        if (!flock($fp, LOCK_EX)) {
            throw new \Exception("获取文件锁失败！");
        }
        foreach (self::$message as $msg) {
            fwrite($fp, $msg);
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        if (filesize($file) >= self::$config['max_file_size'] * 1024 * 1024) {
            rename($file, dirname($file) . DIRECTORY_SEPARATOR . basename($file) . "-bak-" . date('Ymdhis', time()));
        }
        self::$message = [];
    }
}