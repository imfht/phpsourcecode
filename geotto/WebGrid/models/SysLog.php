<?php
include_once("Basic.php");
/*
 * 该类用于管理系统日志数据库
 * */
 
 class SysLog extends Basic{
	 function __construct($dbc){
		parent::__construct($dbc); 
	}
    
    //记录日志信息
    function log($msg){
    	$no = $msg->getNo();
        $content = $msg->getContent();
        $detail = $msg->getDetail();
        $generate_time = $msg->getGenerateTime();
        
        $detail = ($detail == null)?'NULL':"'$detail'";
        $detail = mysqli_real_escape_string($this->dbc, $detail);
        
        $query = "INSERT INTO syslog(no,content,detail,generate_time) VALUES($no,'$content',$detail,'$generate_time')";
        mysqli_query($this->dbc, $query);
        $num = mysqli_affected_rows($this->dbc);
        if($num != 1){
        	return false;
        }
        
        return true;
    }
}
 ?>
