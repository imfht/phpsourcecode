<?php

class Logger
{
    const LOG_FILE_NAME = '%s_log_%s.log';
    const SINGLE_LOG_FILE_NAME = '%s_log.log';
    const LOG_CONTENT_FORMAT = '%s %s %s';

    static $log_file_path = 'logs/';
    static $flag_file_name = 'default';
    static $single = false;
    static $init = false;
    static $output_log = false;
    static $current_log = [];
    static $log_buffer_length;
    static $log_buffer = [];
    static $is_Load = false;

    /**
     * emerg 严重错误，导致系统崩溃无法使用
     * alert 警戒性错误， 必须被立即修改的错误
     * crit 临界值错误， 超过临界值的错误，例如一天24小时，而输入的是25小时这样
     * err 一般性错误
     * warn  警告性错误， 需要发出警告的错误
     * notice 通知，程序可以运行但是还不够完美的错误
     * info 信息，程序输出信息
     * debug 调试，用于调试信息
     * sql SQL语句
     */
    const EMERG = 'emerg';
    const ALERT = 'alert';
    const CRIT = 'crit';
    const ERR = 'err';
    const WARN = 'warn';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';
    const SQL = 'sql';
    const OUTPUT = '__output';

    /**
     * 初始化日志
     * @param string $flag 日志文件标志
     * @param bool $output_log 是否记录输出
     * @param int $buffer_length buff行数
     */
    public static function init($flag, $output_log = false, $buffer_length = 100)
    {
        static::$flag_file_name = $flag;
        if ($output_log) {
            static::$output_log = true;
            ob_start(['\Logger', 'outputCallback']);
        }

        register_shutdown_function(['\Logger', 'flushLog']);

        static::$log_buffer_length = $buffer_length;
    }

    public static function iniSet($config)
    {
        !empty($config['file_path']) && (static::$log_file_path = $config['file_path']);
        !empty($config['file_name']) && (static::$flag_file_name = $config['file_name']);
        if (!is_dir(static::$log_file_path)) {
            @mkdir(static::$log_file_path);
        }

        static::$is_Load = true;
    }

    public static function l($log_type, $contents)
    {
        static::logs($log_type, $contents);
    }

    public static function s($log_type, $contents)
    {
        static::singleLogs($log_type, $contents);
    }

    public static function singleLogs($log_type, $contents)
    {
        static::$single = true;
        static::logs($log_type, $contents);
    }

    public static function logs($log_type, $contents)
    {
        !static::$is_Load && static::iniSet([]);

        static::$current_log['log_type'] = $log_type;
        static::$current_log['log_dt'] = date(DateTime::RFC850);

        !empty($contents) && static::append($contents);

        static::$log_buffer[] = static::$current_log;
        static::$current_log = [];

        count(static::$log_buffer) >= static::$log_buffer_length && static::flushLog();
    }

    public static function append($log_info)
    {
        if (is_string($log_info)) {
            static::$current_log['content'][] = $log_info;
        } else if (is_array($log_info)) {
            foreach ($log_info as $key_name => $key_value) {
                if (is_null($key_value)) {
                    $key_value = 'null';
                }

                if (!is_numeric($key_value) && !is_string($key_value)) {
                    $key_value = json_encode($key_value);
                }

                static::$current_log['content'][] = $key_name . ' ' . $key_value;
            }
        }
    }

    private static function logFileName()
    {
        return static::$single ?
            sprintf(static::SINGLE_LOG_FILE_NAME, static::$flag_file_name) :
            sprintf(static::LOG_FILE_NAME, static::$flag_file_name, date('Ymd'));
    }

    public static function infoString()
    {
        $get = $uri = $user_agent = $x_for_ip = $client_ip = $ip = '-';
        if (!empty($_GET)) {
            $get = json_encode($_GET);
        }

        if (!empty($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
        }

        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }

        if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $x_for_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }

        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $client_ip = $_SERVER["HTTP_CLIENT_IP"];
        }

        if (!empty($_SERVER["REMOTE_ADDR"])) {
            $ip = $_SERVER["REMOTE_ADDR"];
        }

        return $get . ' ' . $uri . ' ' . $user_agent . ' ' . $x_for_ip . ' ' . $client_ip . ' ' . $ip;
    }

    public static function flushLog()
    {
        $write_content = '';
        foreach (static::$log_buffer as &$log_info) {

            if (empty($log_info['content'])) {
                $log_info['content'] = array();
                continue;
            }
            $write_content .= $log_info['log_dt'] . ' ' . $log_info['log_type'] . ' ' . implode($log_info['content'], ' ') . ' ' . static::infoString() . PHP_EOL;
        }
        static::$log_buffer = [];

        $file_name = static::$log_file_path . static::logFileName();
        file_put_contents($file_name, $write_content, FILE_APPEND);
    }

    public static function outputCallback($output_content)
    {
        $output_content = str_replace(PHP_EOL, '\\\\', $output_content);
        static::logs(static::OUTPUT, $output_content);
        static::flushLog();

        return false;
    }
}
