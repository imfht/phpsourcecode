<?php
/**
 * 支持将日志输出到多个Route的Log 
 * 
 * @author xiangyang.yang@snda.com
 * 
 * 配置文件:
 * 
 * // 日志的级别
 * $config['log_threshold'] = 'debug,info,warning,error,trace';
 * 
 * // 日志Route
 * $config['log_routes'] = array(
 *    // 将error, warning 级别的日志保存到文件
 *	  'file'=>array(
 *		  'log_path'=>realpath(APPPATH.'logs'),
 *		  'log_levels'=>'error,warning',
 *	  ),
 *    // 将debug,info,warning,error级别的日志输出到firephp
 *	  'firephp'=>array(
 *		  'log_levels'=>'debug,info,warning,error',
 *	  )
 * );
 */

/**
 * 刷出日志
 */
function log_flush()
{
	load_class('Log')->flush();
}

/**
 * Log系统
 * 
 */
class MY_Log
{
	const LEVEL_TRACE='trace';
	const LEVEL_DEBUG='debug';
	const LEVEL_INFO='info';
	const LEVEL_WARNING='warning';
	const LEVEL_ERROR='error';

	/**
	 * 日志级别，默认只输出错误
	 * 
	 * @var int
	 */
	public $log_threshold='';
	/**
	 * @var integer 
	 * 
	 * 默认为5000， 表示每记录5000条日志后就会释放内存并输出日志
	 */
	public $auto_flush=5000;

	private $_routes=array();
	private $_logs=array();
	private $_logCount=0;
	private $_levels;
	private $_initialized=false;
	
	/**
	 * __construct
	 */
	public function __construct()
	{
		$config=&get_config();
		
		if(!empty($config['log_threshold']))
		{
			if(is_string($config['log_threshold']))
				$this->log_threshold=array_flip(preg_split('/[\s,]+/',strtolower($config['log_threshold']),-1,PREG_SPLIT_NO_EMPTY));
			else if(is_numeric($config['log_threshold']))
			{
				// 兼容CI_Log的配置 log_threshold
				$log_threshold=array(
					'1'=>array(self::LEVEL_ERROR=>true),
					'2'=>array(self::LEVEL_ERROR=>true, self::LEVEL_DEBUG=>true),
					'3'=>array(self::LEVEL_ERROR=>true, self::LEVEL_DEBUG=>true, self::LEVEL_INFO=>true),
					'4'=>array(self::LEVEL_ERROR=>true, self::LEVEL_DEBUG=>true, self::LEVEL_INFO=>true, self::LEVEL_WARNING, self::LEVEL_TRACE)
				);
				if(isset($log_threshold[$config['log_threshold']]))
					$this->log_threshold=$log_threshold[$config['log_threshold']];
			}
			
			// 有配置log_routes
			if(isset($config['log_routes']))
			{
				foreach($config['log_routes'] as $route_class=>$route_config)
				{
					$class='Log_Route_'.ucfirst($route_class);
					if(!class_exists($class, false))
						require APPPATH.'libraries/logging/'.$route_class.EXT;
					$route=new $class;
					if(!empty($config['log_date_format']))
						$route->log_date_format=$config['log_date_format'];
					foreach($route_config as $key=>$val)
						$route->$key=$val;
					$route->init();
					$this->_routes[]=$route;
				}
			}
			// 没有配置log_routes
			// 就使用Log_Route_File,兼容CI_Log
			else
			{
				$route=new Log_Route_File();
				// 兼容CI_Log的配置 log_date_format
				if(!empty($config['log_date_format']))
					$route->log_data_format=$config['log_date_format'];
				$route->log_path = (isset($config['log_path'])&&$config['log_path']!='') ? $config['log_path'] : APPPATH.'logs';
				$route->init();
				$this->_routes[]=$route;
			}
			
			if(isset($config['auto_flush']))
				$this->auto_flush=$config['auto_flush'];
		}
	}
    // --------------------------------------------------------------------
	/**
	 * 可以用这样的方式调用:
	 *  $log =& load_class('Log');
	 *  $log->error($message);
	 *  $log->debug($message);
	 * 
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($name,$arguments)
	{
		$this->write_log($name,$arguments[0]);
	}
    // --------------------------------------------------------------------
	/**
	 * 写日志
	 * 
	 * @param string $level
	 * @param string $message
	 * @param boolean $php_error
	 */
	public function write_log($level, $message, $php_error=null)
	{
		if(!$this->_initialized && isset($GLOBALS['EXT']))
			$this->_initialize($GLOBALS['EXT']);

		$level=strtolower($level);
		if($this->log_threshold && isset($this->log_threshold[$level]))
		{
			$this->_logs[]=array($message,$level,microtime(true));
			$this->_logCount++;

			if($this->auto_flush>0 && $this->_logCount>=$this->auto_flush)
				$this->flush();
		}
	}
    // --------------------------------------------------------------------
	/**
	 * 注册钩子事件，用于在显示输出前和程序执行完毕后刷出日志
	 * 比如FirePHP的日志，需要在显示输出前刷出，
	 * 而文件日志，需要在请求执行完毕后写入日志。
	 * 
	 * @param Hook $hook
	 */
	private function _initialize($hook)
	{
		if(!$this->_initialized)
		{
			$hook->enabled=true;
			$hook->hooks['post_controller'][]=array(
				'class'=>'',
				'function'=>'log_flush',
				'filename'=>'MY_Log.php',
				'filepath'=>'libraries',
				'params'=>'',
			);
			$hook->hooks['post_system'][]=array(
				'class'=>'',
				'function'=>'log_flush',
				'filename'=>'MY_Log.php',
				'filepath'=>'libraries',
				'params'=>'',
			);
		}
		$this->_initialized=true;
	}
    // --------------------------------------------------------------------
	/**
	 * 输出日志到Route
	 */
	public function flush()
	{
		if(!$this->_logCount)
			return;

		foreach($this->_routes as $route)
		{
			if($route->enabled)
				$route->collectLogs($this,true);
		}
		$this->_logs=array();
		$this->_logCount=0;
	}
    // --------------------------------------------------------------------
	/**
	 * 取得Log
	 *
	 * @param string $levels level filter
	 * @return array 返回日志列表，没条日志如下面的结构
	 * 
	 * array(
	 *   [0] => message (string)
	 *   [1] => level (string)
	 *   [2] => timestamp (float, obtained by microtime(true));
	 */
	public function getLogs($levels='')
	{
		if(empty($levels))
			return $this->_logs;			
		$this->_levels=preg_split('/[\s,]+/',strtolower($levels),-1,PREG_SPLIT_NO_EMPTY);
		return array_values(array_filter(array_filter($this->_logs,array($this,'filterByLevel'))));
	}
    // --------------------------------------------------------------------
	/**
	 * 通过日志的级别过滤日志
	 * 
	 * @param array $value
	 */
	private function filterByLevel($value)
	{
		return in_array(strtolower($value[1]),$this->_levels)?$value:false;
	}
}
// --------------------------------------------------------------------
/* End of file MY_Log.php */
/* Location: ./app/admin/libraries/ MY_Log.php */
/**
 * 日志路由，自定义Route应该继承该类，并实现processLogs方法
 * 
 * @author xiangyang.yang
 */
abstract class Log_Route
{
	/**
	 * 是否开启
	 * 
	 * @var boolean
	 */
	public $enabled=true;
	/**
	 * 日志的级别
	 * 
	 * @var mixed 
	 */
	public $log_levels='';
	/**
	 * 日志的时间格式
	 * 
	 * @var string
	 */
	public $log_date_format='Y-m-d H:i:s';
	/**
	 * 收集得到的日志
	 * 
	 * @var array
	 */
	public $logs;

	/**
	 * 初始化
	 */
	public function init()
	{

	}
    // ------------------------------------------------------------------------
	/**
	 * 格式化日志内容
	 * 
	 * @param string $message message content
	 * @param integer $level message level
	 * @param integer $time timestamp
	 * @return string formatted message
	 */
	protected function formatLogMessage($message,$level,$time)
	{
		return @date($this->log_date_format,$time)." [$level] $message\n";
	}
    // ------------------------------------------------------------------------
	/**
	 * 收集日志
	 * 
	 * @param Log $logger Log的实例
	 * @param boolean $processLogs 是否处理log
	 */
	public function collectLogs($logger, $processLogs=false)
	{
		$logs=$logger->getLogs($this->log_levels);
		$this->logs=empty($this->logs) ? $logs : array_merge($this->logs,$logs);
		if($processLogs && !empty($this->logs))
		{
			$this->processLogs($this->logs);
			$this->logs=array();
		}
	}
	
	abstract protected function processLogs($logs);
}
// ------------------------------------------------------------------------

/**
 * 将Log输出到文件
 * 
 * @author xiangyang.yang
 *
 */
class Log_Route_File extends Log_Route
{
	/**
	 * 存储路径
	 * 
	 * @var string
	 */
	public $log_path;
	/**
	 * 存储文件名
	 * 
	 * @var string
	 */
	public $log_file;
	/**
	 * 最大文件尺寸，为0表示不限制
	 * 否则当一个文件超过最大尺寸后就要生成新文件
	 * 
	 * @var integer
	 */
	public $max_file_size=1024; // in KB

	/**
	 * 初始化
	 */
	public function init()
	{
		if(!$this->log_path || !is_dir($this->log_path) || !is_really_writable($this->log_path))
			$this->enabled=FALSE;

		if($this->enabled)
		{
			$this->log_path=trim($this->log_path,'/\\');
			if(empty($this->log_file))
				$this->log_file='log-'.date('Y-m-d').EXT;  // 默认的文件名
		}
	}
    // ------------------------------------------------------------------------
	/**
	 * 将日志保存到文件
	 * 
	 * @param array $logs list of log messages
	 */
	protected function processLogs($logs)
	{
		$logFile=$this->log_path.DIRECTORY_SEPARATOR.$this->log_file;
		if($this->max_file_size>0 && @filesize($logFile)>$this->max_file_size*1024)  // 保证每个日志文件不会太大
			$this->rotateFiles();
		$fp=@fopen($logFile,'ab');
		@flock($fp,LOCK_EX);
		foreach($logs as $log)
			@fwrite($fp,$this->formatLogMessage($log[0],$log[1],$log[2]));
		@flock($fp,LOCK_UN);
		@fclose($fp);
	}
    // ------------------------------------------------------------------------
	/**
	 * Rotates log files.
	 */
	protected function rotateFiles()
	{
		$file=$this->log_path.DIRECTORY_SEPARATOR.$this->log_file;
		for($i=5;$i>0;--$i)
		{
			$rotateFile=$file.'.'.$i;
			if(is_file($rotateFile))
			{
				// suppress errors because it's possible multiple processes enter into this section
				if($i===$max){
					@unlink($rotateFile);
                }else{
					@rename($rotateFile,$file.'.'.($i+1));
			}   }
		}
		if(is_file($file))
			@rename($file,$file.'.1'); // suppress errors because it's possible multiple processes enter into this section
	}
    // ------------------------------------------------------------------------
}


