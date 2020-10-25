<?php 
namespace Ysf;
class Error{
	
	/**
	 * error handle
	 * @param  [type] $errno   [description]
	 * @param  [type] $errstr  [description]
	 * @param  [type] $errfile [description]
	 * @param  [type] $errline [description]
	 * @return [type]          [description]
	 */
	public static function app_error($errno, $errstr, $errfile, $errline,$vars)
	{
		throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	/**
	 * exception handle
	 * @param  [type] $e [description]
	 * @return [type]    [description]
	 */
	public static function app_exception($e){
		$error = [];
		$error['message'] = $e->getMessage();
		$trace = $e->getTrace();
		if('E'==$trace[0]['function']) {
			$error['file'] = $trace[0]['file'];
			$error['line'] = $trace[0]['line'];
		}else{
			$error['file'] = $e->getFile();
			$error['line'] = $e->getLine();
		}
		$error['trace'] = $e->getTraceAsString();
		self::halt($error);
	}
	
	/**
	 * error echo
	 * @param  [type] $error [description]
	 * @return [type]        [description]
	 */
	public static function halt($error){
		$GLOBALS['core']['_stop_time'] = microtime(true);
		$GLOBALS['core']['_stop_memory'] = memory_get_usage();
		#TODO distinguish between cli and web
		#TODO write log
		if (defined('APP_MODE') && APP_MODE=='DEV') {
			echo "<style>*{padding:0;margin:0}body{color:#333;font-size:16px;font-family:\"Menlo\",\"Liberation Mono\",\"Consolas\",\"Courier New\",\"andale mono\",\"lucida console\",\"microsoft yahei\",monospace;font-weight:normal;padding:30px;}h1{font-size:90px;font-family:\"宋体\"}.error_tag{display:inline-block;width:60px;font-weight:700}</style>";
			echo "<h1>Error</h1><br />";
			echo "<span class=\"error_tag\">Message</span> : {$error['message']}<br />";
			echo "<span class=\"error_tag\">File</span> : {$error['file']}<br />";	
			echo "<span class=\"error_tag\">Line</span> : {$error['line']}<br />";
			echo "<span class=\"error_tag\">Trace</span> :<br/> ".str_replace("\n", "<br/>", $error['trace']);
			echo '<br/><br/><br/><span class="error_tag">Time</span> : ' . round($GLOBALS['core']['_stop_time'] - $GLOBALS['core']['_begin_time'],14) . 's<br/>';
			echo '<span class="error_tag">Memory</span> : ' . round(memory_get_peak_usage()/1024,0).'kb<br/>';
			echo '<br/>Powered by <a href="http://framework.yueser.com" targer="_blank">Ysf</a> Ver['.YSF_VERSION.']';
			exit();
		}else{
			// 发送错误信息
			header('HTTP/1.1 500 Internal Server Error'); 
			header('Status:500 Internal Server Error');
			exit('Internal Server Error');
		}
	}
}