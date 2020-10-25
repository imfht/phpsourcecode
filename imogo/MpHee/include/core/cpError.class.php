<?php
//cp错误类
class cpError extends Exception {
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
	static public function show($message="", $code=0) {
		if( function_exists('cp_error_ext') ){
			cp_error_ext($message, $code);
		}else{
			new cpError($message, $code);
		}
    }
		
	//记录错误信息
	static public function write($message){		
		$log_path = cpConfig::get('LOG_PATH');
		//检查日志记录目录是否存在
		 if( !is_dir($log_path) ) {
			//创建日志记录目录
			@mkdir($log_path, 0777, true);
		 }
		 $time=date('Y-m-d H:i:s');
		 $ip= function_exists('get_client_ip') ? get_client_ip() : $_SERVER['REMOTE_ADDR'];
		 $destination =$log_path  . date("Y-m-d_") . md5($log_path). ".log";
		 //写入文件，记录错误信息
       	 @error_log("{$time} | {$ip} | {$_SERVER['PHP_SELF']} |{$message}\r\n", 3,$destination);
	}
	
	//输出错误信息
     protected function showError(){
		//如果开启了日志记录，则写入日志
		if( cpConfig::get('LOG_ON') ) {
			self::write($this->message);
		}
			
		$error_url = cpConfig::get('ERROR_URL');
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
		
		if( defined('DEBUG') && false == DEBUG) {
			@header("HTTP/1.1 404 Not Found");
			exit;
		}
		
		if( !defined('__APP__') ) define( '__APP__' , '/');

	echo 	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>错误提示!</title>
<STYLE>
BODY{FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif; COLOR: #333; background:#fff; FONT-SIZE: 12px;padding:0px;margin:0px;}
A{text-decoration:none;color:#3071BF}
A:hover{text-decoration:underline;}
.error_title{border-bottom:1px #eee solid;font-size:20px;line-height:28px; height:28px;font-weight:600}
.error_box{border-left:3px solid #FC0;font-size:14px; line-height:22px; padding:6px 15px;background:#FFE}
.error_tip{margin-top:15px;padding:6px;font-size:12px;padding-left:15px;background:#f7f7f7}
</STYLE>
</head>
<body>
	<div style="margin:30px auto; width:800px;">
	<div class="error_title">错误提示</div>
	<div style="height:10px"></div>
	<div class="error_box">出错信息：'.$this->message.'</div>';
	//开启调试模式之后，显示详细信息
	if( ($this->errorCode>0) && ($this->errorCode!=404) && cpConfig::get('DEBUG') ) {
	 echo  '<div class="error_box">出错文件：'.$this->errorFile.'</div>
		<div class="error_box">错误行：'.$this->errorLine.'</div>
		<div class="error_box">错误级别：'.$this->errorLevel.'</div>
		<div class="error_box">Trace信息：<br>'.$this->trace.'</div>';
	}
echo '
<div class="error_tip">您可以选择 &nbsp;&nbsp;<a href="'.$_SERVER['PHP_SELF'].'" title="重试">重试</a>&nbsp;&nbsp;<a href="javascript:history.back()" title="返回">返回</a>&nbsp;&nbsp;或者&nbsp;&nbsp;<a href="'.__APP__.'" title="回到首页">回到首页</a> </div>
</div>
</body>
</html>';
		exit;
    }
}