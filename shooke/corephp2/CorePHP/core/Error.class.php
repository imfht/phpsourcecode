<?php
namespace Core;
use \Exception;
/**
 * @author shooke
 * 错误处理类
 * 处理框架错误，输出显示
 */
class Error extends Exception {
    private $errorMessage = '';
    private $errorFile = '';
    private $errorLine = 0;
    private $errorCode = '';
    private $errorLevel = '';
 	private $trace = '';

    public function __construct($errorMessage, $errorCode = 0, $errorFile = '', $errorLine = 0) {
        parent::__construct($errorMessage, $errorCode);
        $this->errorMessage = $errorMessage;
		$this->errorCode = $errorCode == 0?$this->getCode() : $errorCode;
        $this->errorFile = $errorFile == ''?$this->getFile() : $errorFile;
        $this->errorLine = $errorLine == 0?$this->getLine() : $errorLine;
      	$this->errorLevel = $this->getLevel();
 	    $this->trace = $this->trace();
        $this->showError();
    }
	
	//获取trace信息
	protected function trace() {
        $trace = $this->getTrace();

        $traceInfo='';
        $time = date("Y-m-d H:i:s");
        foreach($trace as $t) {
            $traceInfo .= '['.$time.'] ' . $t['file'] . ' (' . $t['line'] . ') ';
            $traceInfo .= $t['class'] . $t['type'] . $t['function'] . '(';
            $traceInfo .= ")<br />\r\n";
        }
		return $traceInfo ;
    }
	
	//错误等级
	protected function getLevel() {
	  $Level_array = array(	1=> '致命错误(E_ERROR)',
			2 => '警告(E_WARNING)',
			4 => '语法解析错误(E_PARSE)',  
			8 => '提示(E_NOTICE)',  
			16 => 'E_CORE_ERROR',  
			32 => 'E_CORE_WARNING',  
			64 => '编译错误(E_COMPILE_ERROR)', 
			128 => '编译警告(E_COMPILE_WARNING)',  
			256 => '致命错误(E_USER_ERROR)',  
			512 => '警告(E_USER_WARNING)', 
			1024 => '提示(E_USER_NOTICE)',  
			2047 => 'E_ALL', 
			2048 => 'E_STRICT'
		 );
		return isset( $Level_array[$this->errorCode] ) ? $Level_array[$this->errorCode] : $this->errorCode;
	}
	
	//抛出错误信息，用于外部调用
	static public function show($message="") {
		 new Error($message);
    }
		
	//记录错误信息
	static public function write($message){		
		$log_path = Config::get('LOG_PATH');
		//检查日志记录目录是否存在
		 if( !is_dir($log_path) ) {
			//创建日志记录目录
			@mkdir($log_path, 0777, true);
		 }
		 $time=date('Y-m-d H:i:s');
		 $ip= HttpRequest::getClientIp();
		 $destination =$log_path  . date("Y-m-d_") . md5($log_path). ".log";
		 //写入文件，记录错误信息
       	 @error_log("{$time} | {$ip} | {$_SERVER['PHP_SELF']} |{$message}\r\n", 3,$destination);
	}
	
	//输出错误信息
     protected function showError(){
		//如果开启了日志记录，则写入日志
		if( Config::get('LOG_ON') ) {
			self::write($this->message);
		}
			
		$error_url = Config::get('ERROR_URL');
		//错误页面重定向
		if($error_url != ''){
			echo '<script language="javascript">
				if(self!=top){
				  parent.location.href="'.$error_url.'";
				} else {
				 window.location.href="'.$error_url.'";
				}
				</script>';
			exit;
		}
		
		if( DEBUG == false) {
			@header("HTTP/1.1 404 Not Found");
			exit;
		}
		
		if( !defined('__APP__') ) define( '__APP__' , '/');
        //变量赋值		
		$message = $this->message;
		$errorCode = $this->errorCode;
		$errorFile = $this->errorFile;
		$errorLine = $this->errorLine;
		$errorLevel = $this->errorLevel;
		$trace = $this->trace;
	    include 'Tpl/Error.php';
		exit;
    }
}