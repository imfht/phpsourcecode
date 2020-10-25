<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
	define('__Index__',dirname(__FILE__));
	if(@!include_once(__Index__."/Public/Uploadfile/Done.lock")){
		header("location:./Mark.php");
	}else{
	include __Index__.'/Index/Action/Index_Index_Action.php';
	}
?> 