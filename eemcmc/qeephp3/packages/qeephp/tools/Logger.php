<?php namespace qeephp\tools;

use qeephp\Config;

class Logger implements ILogger
{
    private $_date_format;
    private $_log = array();
    private $_cached_size;
    private $_cache_chunk_size;
    private $_filename;
    private $_level;
    private $_alert;
    private $_name;

    private static $_level_names = array(
        1 => 'TRACE',
        2 => 'DBEUG',
        3 => 'INFO',
        4 => 'WARN',
        5 => 'ERROR',
        6 => 'FATAL',
    );

    private function __construct($name)
    {
        $config = Config::get("logger.{$name}");
        $this->_filename = $config['filename'];
        $this->_cache_chunk_size = intval(val($config, 'cache_chunk_size', 65536));
        $this->_date_format = val($config, 'date_format', 'Y-m-d H:i:s');
        $this->_cached_size = 0;
        $this->_level = intval(val($config, 'level', self::WARN));
        $alert = val($config, 'alert', false);
        if ( !empty($alert) && is_callable($alert))
        {
            $this->_alert = $alert;
        }
        $this->_name = $name;
        $this->_startup = time();
        $this->_shutdown = false;$pathinfo = pathinfo($this->_filename);

        if (is_readable($this->_filename))
        {
            # 检查文件是否已经超过指定大小
            $filesize = file_exists($this->_filename) ? filesize($this->_filename) : 0;
            
            $maxsize = (int)val($config,'file_maxsize',512);
            if ($maxsize > 0) {
                $maxsize = $maxsize * 1024;
                if ($filesize >= $maxsize) {
                    # 使用新的日志文件名
                    $filename = $this->_filename . '.' . date('ymdHis');
                    # 如果不能重命名则使用之前的文件
                    @rename($this->_filename, $filename);
                }
            }
        }
    }

    function __destruct()
    {
        $this->_shutdown = TRUE;
        $this->flush();
    }

    /**
     * 返回指定的日志服务对象实例
     *
     * @param string $name
     *
     * @return ILogger
     */
    static function instance($name)
    {
        static $instances = array();
        if (!isset($instances[$name]))
        {
            $instances[$name] = new Logger($name);
        }
        return $instances[$name];
    }

    static function getLevelName($level)
    {
        return self::$_level_names[$level];
    }

    function trace($message)
    {
        $this->log(self::TRACE, $message);
    }

    function debug($message)
    {
        $this->log(self::DEBUG, $message);
    }

    function info($message)
    {
        $this->log(self::INFO, $message);
    }

    function warn($message)
    {
        $this->log(self::WARN, $message);
    }

    function error($message)
    {
        $this->log(self::ERROR, $message);
    }

    function fatal($message)
    {
        $this->log(self::FATAL, $message);
    }

    function log($level, $message)
    {
        $now = time();
        if ($this->_alert)
        {
            # 捕获上下文的错误提示信息
            call_user_func_array($this->_alert, array($level, $message, $now, $this->_name));
        }

        if ($level < $this->_level) return;

        $message = print_r($message, true);

        $this->_log[] = array($level, $now, $message);

        $this->_cached_size += strlen($message);
        if ($this->_cached_size >= $this->_cache_chunk_size)
        {
            $this->flush();
        }
    }

    function flush()
    {
        if (empty($this->_log)) return;
        if ( $this->_startup > 0 )
        {
            $url = empty($_SERVER['REQUEST_URI']) ? '' : $_SERVER['REQUEST_URI'];
            $string = sprintf("[START] %s: %s\n",date($this->_date_format, $this->_startup),"--- {$url} ---");
            $this->_startup = 0;
        }
        else
        {
            $string = '';
        }

        foreach ($this->_log as $offset => $item)
        {
            unset($this->_log[$offset]);
            list($level, $time, $message) = $item;
            $level = self::$_level_names[$level];
            $string .= "[$level] ";
            $string .= date($this->_date_format, $time);
            $string .= ": {$message}\n";
        }
        if ( $this->_shutdown )
        {
            $string .= sprintf("[END] %s \n",date($this->_date_format));
        }

        $fp = @fopen($this->_filename, 'a');
        if ($fp && @flock($fp, LOCK_EX))
        {
            @fwrite($fp, $string);
            @flock($fp, LOCK_UN);
            @fclose($fp);
        }
        $this->_log = array();
        $this->_cached_size = 0;
    }

}