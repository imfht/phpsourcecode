<?php
namespace Ext;
//Log::runlog('log','说明信息');
class Log {
	private static $time; 
	private static $logpath;
	public static function runlog($file, $message, $halt=0) {			
		if(!Config::get('LOG_ON')) return true;//判断日志是否开启
		self::$logpath=Config::get('LOG_PATH');		
		self::$time = time();
		$nowurl = $_SERVER['REQUEST_URI']?$_SERVER['REQUEST_URI']:($_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME']);
		$log = date('Y-m-d H:i:s',self::$time)."\t$nowurl\t".str_replace(array("\r", "\n"), array(' ', ' '), trim($message));		
		Log::writelog($file, $log);
		if($halt) {
			exit();
		}
	}


	public static function writelog($file, $log) {		
		$yearmonth = date( 'Ym', time());
		$logdir = self::$logpath;
	
		self::makeDir($logdir);
		$logfile = $logdir.$yearmonth.'_'.$file.'.php';
		if(@filesize($logfile) > 2048000) {
			$dir = opendir($logdir);
			$length = strlen($file);
			$maxid = $id = 0;
			while($entry = readdir($dir)) {
				if(strpos($entry, $yearmonth.'_'.$file) !== false) {
					$id = intval(substr($entry, $length + 8, -4));
					$id > $maxid && $maxid = $id;
				}
			}
			closedir($dir);

			$logfilebak = $logdir.$yearmonth.'_'.$file.'_'.($maxid + 1).'.php';
			@rename($logfile, $logfilebak);
		}
		if($fp = @fopen($logfile, 'a')) {
			@flock($fp, 2);
			if(!is_array($log)) {
				$log = array($log);
			}
			foreach($log as $tmp) {
				fwrite($fp, "<?PHP exit;?>\t".str_replace(array('<?', '?>'), '', $tmp)."\n");
			}
			fclose($fp);
		}
	}
	public static function makeDir( $dir, $mode = 0777 ) {
		if( ! $dir ) return 0;
		$dir = str_replace( "\\", "/", $dir );
		$mdir = "";
		foreach( explode( "/", $dir ) as $val ) {
			$mdir .= $val."/";
			if( $val == ".." || $val == "." || trim( $val ) == "" ) continue;

			if( ! file_exists( $mdir ) ) {
				if(!@mkdir( $mdir, $mode )){
					return false;
				}
			}
		}
		return true;
	}

}
