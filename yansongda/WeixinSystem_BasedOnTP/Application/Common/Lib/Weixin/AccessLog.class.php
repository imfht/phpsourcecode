<?php
namespace Common\Lib\Weixin;

/**
* 	
*/
class AccessLog
{
	public function __construct()
	{
		if ( !function_exists('memcache_init')) {
			$this->write();
		} else {
			return true;
		}
	}

	public static function write()
	{
		$msg = "[ ".date('Y-m-d H:m:s')." ]\r\n";
		$msg .= "URL:".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."\r\n";
		$msg .= "METHOD:".$_SERVER['REQUEST_METHOD']."\r\n";
		$msg .= "CLINT_IP:".get_client_ip()."\r\n\r\n";
		$dest = LOG_PATH."Access/".date('y_m_d').'.log';
		return error_log($msg, 3, $dest);
	}
}