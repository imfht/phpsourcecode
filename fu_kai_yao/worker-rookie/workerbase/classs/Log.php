<?php 
namespace workerbase\classs;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Formatter\JsonFormatter;

/**
 * 日志类
 * Class Log
 * @package workerbase\classs
 * @method void error($msg, array $context = array()) static 记录错误日志
 * @method void info($msg, array $context = array()) static 记录一般信息日志
 * @method void notice($msg, array $context = array()) static 记录提示日志
 * @method void alert($msg, array $context = array()) static 记录报警日志
 * @method void debug($msg, array $context = array()) static 记录调试日志
 * @author fukaiyao
 */
class Log 
{
    /**
     * log
     * @var Log
     */
    private static $_instance = null;

    private $_logger = null;

    // $handlers   [日志管理器]
    private $_handlers = array();
    //$processors [日志处理器,在日志后添加处理信息]
    private $_processors = array();

    //请求id
    private static $_requireId = null;

    /**
     * [__construct description]
     * @param string $name       [日志名称]
     * @param [type] $path       [路径]
     */
	function __construct($name = ''){
        $config = Config::read('log');
        $config['name'] = $name;
        $this->init($config);
	}

    /**
     * 日志初始化
     * @access public
     * @param  array $config 配置参数
     * @return void
     */
    public function init($config = [])
    {
        $name = isset($config['name']) ? $config['name'] : '';
        $fname = '';
        if (!empty($name)) {
            $fname = '_' . $name;
        }

        if (defined('IS_WK_CRON')) {
            $fname = '_cron' . $fname;
        }
        if (defined('IS_WK_WORKER')) {
            $fname = '_worker' . $fname;
        }

        $filename = $config['path'] . date('Ym') . '/' . date('d') . $fname . '.log';

        try{
            $path = dirname($filename);
            !is_dir($path) && mkdir($path, 0755, true);
        }
        catch (\Exception $e) {
        }

        $stream_handler = new StreamHandler($filename, Logger::DEBUG);
        // $stream_handler->setFormatter(new JsonFormatter());//格式化成json
        array_unshift($this->_handlers, $stream_handler);//加入handler 日志管理器数组，配置管理器

        array_unshift($this->_processors, new WebProcessor);//请求来源的信息
//		array_unshift($this->_processors, new IntrospectionProcessor);//当前打印日志的文件信息
        //在日的后面加上了uid和process_id
//		 array_unshift($this->_processors, new ProcessIdProcessor);
//		array_unshift($this->_processors, new UidProcessor(16));//加入processors 日志管理器数组，配置管理器
//        array_unshift($this->_processors, new PsrLogMessageProcessor);//PSR-3规则处理信息

        if (null === $this->_logger) {
            $this->_logger = new Logger(trim($name,'_'), $this->_handlers, $this->_processors);
        }
    }

    /**
     * 获取log实例
     * @return Log
     */
    public static function getInstance($prefix = null)
    {
        $tag = 'wk';
        if (is_null($prefix)) {
            $prefix = 'info';
        } elseif (!empty($prefix)) {
            $tag = $prefix;
        }

        if (!isset(self::$_instance[$tag])) {
            self::$_instance[$tag] = new Log($prefix);
        }
        return self::$_instance[$tag];
    }

	 /**
     * @return string
     */
    public function getName()
    {
        return $this->_logger->getName();
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string $message The log message
     * @param  array  $context The log context
     * @return bool   Whether the record has been processed
     */
    public static function err($message, array $context = array())
    {
        if (!isset(self::$_instance['error'])) {
            self::$_instance['error'] = new Log('error');
        }
        call_user_func_array([self::$_instance['error']->_logger, 'error'], [$message, $context]);
    }

    /**
     * 实例化方法调用
     * @access public
     * @param  string $method 调用方法
     * @param  mixed  $args   参数
     * @return void
     */
    public function __call($method, $args)
    {
        if (count($args) > 2) {
            return false;
        }

        $msg = $args[0];
        if (is_object($msg) || is_array($msg)) {
            $msg = var_export($msg, true);
        }

        //获取输入日志的上下文环境
        $bt = debug_backtrace(0, 3);

        $msg .= " ["; //附加调试参数
        if (count($bt) == 3) {
            $callContext = $bt[1];

            if (isset($callContext['class'])) {
                $newCategory = $callContext['class'];
                $newCategory .= '::' . $callContext['function'];
                if (isset($callContext['line'])) {
                    $newCategory .= '-' . $callContext['line'];
                }
                $msg .= "class={$newCategory}";
            }

            if (isset($callContext['args'])) {
                //附加函数参数
                $arguments = $callContext['args'];
                if  (!empty($arguments)) {
                    $arguments = json_encode($arguments);
                    $msg .= " args={$arguments}";
                }
            }
        }
        $msg .= ']';

        $callFile = $bt[0];
        $msg .= " ["; //附加调试参数
        if (isset($callFile['file'])) {
            $msg .= "file={$callFile['file']}";
            if (isset($callFile['line'])) {
                $msg .= '-' . $callFile['line'];
            }
        }
        $msg .= ']';

        $msg .= " [requireId=".self::generateRequireId()."]";

        $args[0] = $msg;
        call_user_func_array([$this->_logger, $method], $args);
    }

    /**
     * 静态方法调用
     * @access public
     * @param  string $method 调用方法
     * @param  mixed  $args   参数
     * @return void
     */
    public static function __callStatic($method, $args)
    {
        if (count($args) > 2) {
            return false;
        }

        $msg = $args[0];
        if (is_object($msg) || is_array($msg)) {
            $msg = var_export($msg, true);
        }

        //获取输入日志的上下文环境
        $bt = debug_backtrace(0, 3);

        $msg .= " ["; //附加调试参数
        if (count($bt) == 3) {
            $callContext = $bt[1];

            if (isset($callContext['class'])) {
                $newCategory = $callContext['class'];
                $newCategory .= '::' . $callContext['function'];
                if (isset($callContext['line'])) {
                    $newCategory .= '-' . $callContext['line'];
                }
                $msg .= "class={$newCategory}";
            }

            if (isset($callContext['args'])) {
                //附加函数参数
                $arguments = $callContext['args'];
                if  (!empty($arguments)) {
                    $arguments = json_encode($arguments);
                    $msg .= " args={$arguments}";
                }
            }
        }
        $msg .= ']';

        $callFile = $bt[0];
        $msg .= " ["; //附加调试参数
        if (isset($callFile['file'])) {
            $msg .= "file={$callFile['file']}";
            if (isset($callFile['line'])) {
                $msg .= '-' . $callFile['line'];
            }
        }
        $msg .= ']';

        $msg .= " [requireId=".self::generateRequireId()."]";

        $args[0] = $msg;

        call_user_func_array([self::getInstance($method)->_logger, $method], $args);
    }


    /**
     * 设置请求id
     * @param $requireId
     */
    public static function setRequireId($requireId = null)
    {
        self::$_requireId = null;

        if (is_null($requireId) || empty($requireId)) {
            self::$_requireId = self::getProcessId() . uniqid();
        } else {
            self::$_requireId = $requireId;
        }
    }

    /**
     * 清除请求id
     */
    public static function unSetRequireId()
    {
        self::$_requireId = null;
    }

    //生成请求id
    private static function generateRequireId()
    {
        if (is_null(self::$_requireId) || empty(self::$_requireId)) {
            self::setRequireId();
        }
        return self::$_requireId;
    }

    //进程id
    private static function getProcessId()
    {
        return getmypid();
    }
}