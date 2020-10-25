<?php namespace qeephp\tools;

use qeephp\mvc\App;
use qeephp\debug\AlertFb;

/**
 * 日志报警器
 *
 * 排错的一种手段,为什么有这个类参见: http://vb2005xu.iteye.com/blog/2044467
 */
class LogAlert
{
	/**
     * 是否需要发送警报
     *
     * @var bool
     */
    private static $write = false;

    /**
     * 测试模式下是否启用报警
     *
     * @var bool
     */
    private static $enableInDebug = false;

    /**
     * 报警的日志级别
     *
     * @var int
     */
    private static $level = ILogger::WARN;

    /**
     * 报警发出的应用运行的上下文信息
     *
     * @var array
     */
    private static $traces = array();

    /**
     * 设置 报警的日志级别
     *
     * @param int $level
     */
    static function setLevel($level)
    {
        self::$level = $level;
    }

    /**
     * 测试模式下是否启用报警
     *
     * @param bool $enable
     */
    static function enableInDebug($enable=true)
    {
        self::$enableInDebug = $enable;
    }

    /**
     * 是否需要发送警报
     *
     * @return bool
     */
    static function isWrite()
    {
        return self::$write;
    }

    /**
     * 返回 报警发出的应用运行的上下文信息
     *
     * @return array
     */
    static function getTraces()
    {
        return self::$traces;
    }

	static function alert($level, $message, $time, $logger)
	{
        if (QEE_DEBUG)
        {
            if (APP_IN_CLI)
            {
                self::console($level, $message, $time, $logger);
            }
            else
            {
                self::fb($level, $message, $time, $logger);
            }
            if (! self::$enableInDebug) return;
        }

        self::add_trace($level, $message, $time, $logger);
	}

	private static function fb($level, $message, $time, $logger)
	{
		AlertFb::alert($level, $message);
	}

	private static function html($level, $message, $time, $logger)
	{
		$level = Logger::getLevelName($level);
		echo "<BR />[$level($logger)]: " . print_r($message,true);
	}

	private static function console($level, $message, $time, $logger)
	{
        $level = Logger::getLevelName($level);
		fwrite(STDOUT, "[$level($logger)]: " . print_r($message,true) . PHP_EOL);
	}

	private static function add_trace($level, $message, $time, $logger)
	{
		self::$traces[] = array($level,$time, $message, $logger);
		if ( $level >= self::$level )
		{
            self::$write = true;
		}
	}

}