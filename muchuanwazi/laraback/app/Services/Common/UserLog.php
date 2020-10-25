<?php
namespace App\Services\Common;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\WebProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Formatter\LineFormatter;
use Request;
use Sentry;
use Config;
use Auth;

/**
 * UserLog
 *
 * Custom monolog logger for CMS user : DEBUG,INFO,NOTICE,WARNING,ERROR,CRITICAL,ALERT,EMERGENCY
 *
 * @author     
 */ 
class UserLog {

    /**
	 * write 
	 * @return void
	 */
    public static function debug($log)
    {
    	self::write($log,Logger::DEBUG);
    }
    public static function info($log)
    {
    	self::write($log,Logger::INFO);
    }
    public static function notice($log)
    {
    	self::write($log,Logger::NOTICE);
    }
    public static function warning($log)
    {
    	self::write($log,Logger::WARNING);
    }
    public static function error($log)
    {
    	self::write($log,Logger::ERROR);
    }
    public static function critical($log)
    {
    	self::write($log,Logger::CRITICAL);
    }
    public static function alert($log)
    {
    	self::write($log,Logger::ALERT);
    }
    public static function emergency($log)
    {
    	self::write($log,Logger::EMERGENCY);
    }

	private static function write($logtext='',$level=Logger::INFO)
	{
		if ("yes"==Config::get('app.userlog')) 
		{
			$log = new Logger('userlog');
			// handler init, making days separated logs
			$handler = new RotatingFileHandler(Config::get('app.userlog_path'), 0, $level);		
			// formatter, ordering log rows
			$handler->setFormatter(new LineFormatter("[%datetime%] %channel%.%level_name%: %message% %extra% %context%\n"));
			// add handler to the logger
			$log->pushHandler($handler);
			// processor, adding URI, IP address etc. to the log
			$log->pushProcessor(new WebProcessor);
			// processor, memory usage
			$log->pushProcessor(new MemoryUsageProcessor);

			$userinfo=" [] ";
			$user = Auth::user();
			if($user)
			{
				$userinfo=' [USERID:'.$user->id.'] ';
			}

			$log->addInfo($logtext.$userinfo);		
		}
	}
}