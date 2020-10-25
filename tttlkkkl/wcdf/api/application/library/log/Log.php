<?php
    /**
     * 使用SeaLog，本类旨在做桥接和代码提示
     * Date: 2016/9/23 0023
     * Time: 18:05
     * Author: 李华胜 lihuasheng@wapwei.com
     */
    namespace log;
    use \SeasLog;
    class Log
    {
        public function __construct()
        {
            #SeasLog init
        }

        public function __destruct()
        {
            #SeasLog distroy
        }

        /**
         * 设置basePath
         *
         * @param $basePath
         *
         * @return bool
         */
        static public function setBasePath($basePath)
        {
            return SeasLog::setBasePath($basePath);
        }

        /**
         * 获取basePath
         *
         * @return string
         */
        static public function getBasePath()
        {
            return SeasLog::getBasePath();
        }

        /**
         * 设置模块目录
         * @param $module
         *
         * @return bool
         */
        static public function setLogger($module)
        {
            return SeasLog::setLogger($module);
        }

        /**
         * 获取最后一次设置的模块目录
         * @return string
         */
        static public function getLastLogger()
        {
            return SeasLog::getLastLogger();
        }

        /**
         * 设置DatetimeFormat配置
         * @param $format
         *
         * @return bool
         */
        static public function setDatetimeFormat($format)
        {
            return SeasLog::setDatetimeFormat($format);
        }

        /**
         * 返回当前DatetimeFormat配置格式
         * @return string
         */
        static public function getDatetimeFormat()
        {
            return SeasLog::getDatetimeFormat();
        }

        /**
         * 统计所有类型（或单个类型）行数
         * @param string $level
         * @param string $log_path
         * @param null   $key_word
         *
         * @return array | long
         */
        static public function analyzerCount($level = 'all', $log_path = '*', $key_word = NULL)
        {
            return SeasLog::analyzerCount($level, $log_path, $key_word);
        }

        /**
         * 以数组形式，快速取出某类型log的各行详情
         *
         * @param        $level
         * @param string $log_path
         * @param null   $key_word
         * @param int    $start
         * @param int    $limit
         * @param        $order 默认为正序 SEASLOG_DETAIL_ORDER_ASC，可选倒序 SEASLOG_DETAIL_ORDER_DESC
         *
         * @return array
         */
        static public function analyzerDetail($level = SEASLOG_INFO, $log_path = '*', $key_word = NULL, $start = 1, $limit = 20, $order = SEASLOG_DETAIL_ORDER_ASC)
        {
            return SeasLog::analyzerDetail($level, $log_path, $key_word, $start, $limit, $order);
        }

        /**
         * 获得当前日志buffer中的内容
         *
         * @return array
         */
        static public function getBuffer()
        {
            return SeasLog::getBuffer();
        }

        /**
         * 将buffer中的日志立刻刷到硬盘
         *
         * @return bool
         */
        static public function flushBuffer()
        {
            return SeasLog::flushBuffer();
        }

        /**
         * 记录debug日志
         *
         * @param        $message
         * @param array  $content
         * @param string $module
         */
        static public function debug($message, array $content = array(), $module = '')
        {
            return SeasLog::debug($message, $content, $module);
        }

        /**
         * 记录info日志
         *
         * @param        $message
         * @param array  $content
         * @param string $module
         */
        static public function info($message, array $content = array(), $module = '')
        {
            return SeasLog::info($message, $content, $module);
        }

        /**
         * 记录notice日志
         *
         * @param        $message
         * @param array  $content
         * @param string $module
         */
        static public function notice($message, array $content = array(), $module = '')
        {
            #$level = SEASLOG_NOTICE
            return SeasLog::notice($message, $content, $module);
        }

        /**
         * 记录warning日志
         *
         * @param        $message
         * @param array  $content
         * @param string $module
         */
        static public function warning($message, array $content = array(), $module = '')
        {
            #$level = SEASLOG_WARNING
            return SeasLog::warning($message, $content, $module);
        }

        /**
         * 记录error日志
         *
         * @param        $message
         * @param array  $content
         * @param string $module
         */
        static public function error($message, $content, $module )
        {
            #$level = SEASLOG_ERROR
            return SeasLog::error($message, $content, $module);
        }

        /**
         * 记录critical日志
         *
         * @param        $message
         * @param array  $content
         * @param string $module
         */
        static public function critical($message, array $content = array(), $module = '')
        {
            #$level = SEASLOG_CRITICAL
            return SeasLog::critical($message, $content, $module);
        }

        /**
         * 记录alert日志
         *
         * @param        $message
         * @param array  $content
         * @param string $module
         */
        static public function alert($message, array $content = array(), $module = '')
        {
            #$level = SEASLOG_ALERT
            return SeasLog::alert($message, $content, $module);
        }

        /**
         * 记录emergency日志
         *
         * @param        $message
         * @param array  $content
         * @param string $module
         */
        static public function emergency($message, array $content = array(), $module = '')
        {
            #$level = SEASLOG_EMERGENCY
            return SeasLog::emergency($message, $content, $module);
        }

        /**
         * 通用日志方法
         * @param        $level
         * @param        $message
         * @param array  $content
         * @param string $module
         */
        static public function log($level, $message, array $content = array(), $module = '')
        {
            return SeasLog::log($level, $message, $content, $module);
        }
    }
